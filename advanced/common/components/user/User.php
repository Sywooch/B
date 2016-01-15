<?php
/**
 * 用户模型，实例：Yii::$app->user
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/3
 * Time: 20:07
 */

namespace common\components\user;

use common\components\Counter;
use common\components\Error;
use common\components\Updater;
use common\config\RedisKey;
use common\entities\UserEventEntity;
use common\entities\UserEventLogEntity;
use common\entities\UserGradeRuleEntity;
use common\entities\UserScoreLogEntity;
use common\entities\UserScoreRuleEntity;
use common\exceptions\ModelSaveErrorException;
use common\exceptions\UserEventDoesNotDefineException;
use common\helpers\StringHelper;
use common\helpers\TimeHelper;
use common\models\CacheUserScoreRuleModel;
use common\models\CacheUserEventModel;
use common\models\CacheUserGradeModel;
use common\services\UserService;
use yii\base\Event;
use yii\helpers\Inflector;
use Yii;
use yii\helpers\Json;

class User extends \yii\web\User
{
    const STEP_REGISTER = 'register';
    const STEP_LOGIN = 'login';

    public $user_step;

    
    /**
     * 判断用户当前是否处于注册、登陆环节
     * @return bool
     */
    public function getStep()
    {
        return $this->user_step;
    }

    /**
     * 设置用户当前处于哪个环节
     * @param $step
     * @return mixed
     */
    public function setStep($step)
    {
        return $this->user_step = $step;
    }

    private function getAllScoreRule()
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_SCORE_RULE];
        $cache_data = Yii::$app->redis->get($cache_key);
        $cache_user_score_rule_model = new CacheUserScoreRuleModel();
        if ($cache_data === false) {
            $rules = UserScoreRuleEntity::findAll(['status' => UserScoreRuleEntity::STATUS_ENABLE]);
            $data = [];

            foreach ($rules as $rule) {
                $data = $cache_user_score_rule_model->filter($rule);
                $data[$rule->user_event_id][] = $cache_user_score_rule_model->build($data);
            }

            $cache_data = $data;
            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }

    /**
     * 获取积分规则
     * @param $user_event_id
     * @return CacheUserScoreRuleModel
     */
    private function getUserScoreRule($user_event_id)
    {
        $rules = $this->getAllScoreRule();
        if (isset($rules[$user_event_id])) {
            return $rules[$user_event_id];
        } else {
            return [];
        }
    }

    /**
     * 根据事件名称获取事件
     * @param $event_name
     * @return CacheUserEventModel
     */
    private function getUserEventByEventName($event_name)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_EVENT, $event_name];
        $cache_data = Yii::$app->redis->hGetAll($cache_key);

        $cacheUserEventModel = new CacheUserEventModel();

        if (empty($cache_data)) {
            $data = UserEventEntity::find()->where(
                [
                    'event'  => $event_name,
                    'status' => UserEventEntity::STATUS_ENABLE,
                ]
            )->one();

            if ($data) {
                $cache_data = $cacheUserEventModel->filter($data);
                Yii::$app->redis->hMset($cache_key, $cache_data);
            } else {
                $cache_data = false;
            }
        }

        return $cacheUserEventModel->build($cache_data);
    }

    /**
     * @return array|CacheUserGradeModel
     */
    private function getAllUserGrades()
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_GRADE_RULE];
        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $grades = UserGradeRuleEntity::find()->where(
                ['status' => UserGradeRuleEntity::STATUS_ENABLE]
            )->orderBy('score ASC')->all();

            $cache_data = [];

            $cache_user_grade_model = new CacheUserGradeModel();
            foreach ($grades as $grade) {
                /* @var UserGradeRuleEntity $grade */
                $data = $cache_user_grade_model->filter($grade);
                $cache_data[$grade->id] = $cache_user_grade_model->build($data);
            }

            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }

    /**
     * 计算用户等级
     * @param $credit
     * @param $currency
     * @return int
     */
    private function calculateUserGrade($credit, $currency)
    {
        //todo 用户积分的公式
        $formula = '$credit * 1 + $currency * 2';
        $credit_calculator = create_function('$credit, $currency', 'return ' . $formula . ';');

        //算总分　根据 credit + currency 计算
        $score = $credit_calculator($credit, $currency);

        $all_grades = $this->getAllUserGrades();

        foreach ($all_grades as $grade) {
            /* @var CacheUserGradeModel $grade */
            if ($score < $grade->score) {
                return $grade->id;
            }
        }

        return 0;
    }

    /**
     * 用户事件触发，不得以 before,after开头，YII框架级别的用户事件，使用before,after开头
     * @param string     $name
     * @param Event|null $associate_event
     * @throws UserEventDoesNotDefineException
     */
    public function trigger($name, Event $associate_event = null)
    {
        //触发框架级用户事件
        if (strpos($name, 'before') === 0 || strpos($name, 'after') === 0) {
            return parent::trigger($name, $associate_event);
        } else {
            $event_name = Inflector::underscore($name);

            //检查事件是否存在
            if ($user_event = $this->getUserEventByEventName($event_name)) {
                //用户动态
                $user_event_log_id = $this->dealWithUserEventLog($user_event, $associate_event);
                if ($user_event_log_id) {
                    //用户积分
                    $this->dealWithUserScore($user_event_log_id, $user_event);
                }

            } else {
                throw new UserEventDoesNotDefineException($event_name);
            }
        }
    }

    /**
     * 处理用户积分变动
     * 一个动作可以有多个积分变动规则
     * @param                     $user_event_log_id 事件ID
     * @param CacheUserEventModel $user_event        用户事件
     * @return bool
     * @throws ModelSaveErrorException
     */
    private function dealWithUserScore($user_event_log_id, CacheUserEventModel $user_event)
    {
        $rules = $this->getUserScoreRule($user_event->id);

        foreach ($rules as $rule) {
            if ($rule && $this->checkIfCanExecuteScoreRule($rule)) {
                if (Counter::updateUserScore($this->id, $rule->type, $rule->score)) {
                    //积分日志
                    $this->dealWithUserScoreLog($user_event_log_id, $rule);
                    //用户等级
                    $this->dealWithGrade();
                }
            }
        }

        return true;
    }

    /**
     * @param                         $user_event_log_id 用户事件ID
     * @param CacheUserScoreRuleModel $rule              积分规则
     * @return bool
     * @throws ModelSaveErrorException
     */
    private function dealWithUserScoreLog($user_event_log_id, CacheUserScoreRuleModel $rule)
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');
        $model = new UserScoreLogEntity();
        $data = [
            'user_event_id'     => $rule->user_event_id,
            'user_event_log_id' => $user_event_log_id,
            'type'              => $rule->type,
            'score'             => $rule->score,
            'created_by'        => $this->id,
            'created_at'        => TimeHelper::getCurrentTime(),
        ];

        if ($model->load($data, '') && $model->save()) {
            return true;
        } else {
            throw new ModelSaveErrorException($model);
        }
    }

    /**
     * 判断当前规则是否可以执行
     * 查看数据库，判断是否超过限制次数
     * @param CacheUserScoreRuleModel $rule 积分规则
     * @return bool
     */
    private function checkIfCanExecuteScoreRule(CacheUserScoreRuleModel $rule)
    {
        if ($rule->limit_interval == UserScoreRuleEntity::LIMIT_TYPE_LIMITLESS) {
            return true;
        }

        $query = UserScoreLogEntity::find()->where(
            [
                'user_event_id' => $rule->user_event_id,
                'created_by'    => $this->id,
            ]
        );

        switch ($rule->limit_interval) {
            case UserScoreRuleEntity::LIMIT_TYPE_YEAR:
                $query->andWhere(
                    [
                        'between',
                        'created_at',
                        TimeHelper::getThisYearStartTime(),
                        TimeHelper::getThisYearEndTime(),
                    ]
                );
                break;
            case UserScoreRuleEntity::LIMIT_TYPE_SEASON:
                $query->andWhere(
                    [
                        'between',
                        'created_at',
                        TimeHelper::getThisSeasonStartTime(),
                        TimeHelper::getThisSeasonEndTime(),

                    ]
                );
                break;
            case UserScoreRuleEntity::LIMIT_TYPE_MONTH:
                $query->andWhere(
                    [
                        'between',
                        'created_at',
                        TimeHelper::getThisSeasonStartTime(),
                        TimeHelper::getThisSeasonEndTime(),

                    ]
                );
                break;
            case UserScoreRuleEntity::LIMIT_TYPE_WEEK:
                $query->andWhere(
                    [
                        'between',
                        'created_at',
                        TimeHelper::getThisWeekStartTime(),
                        TimeHelper::getThisWeekEndTime(),

                    ]
                );
                break;
            case UserScoreRuleEntity::LIMIT_TYPE_DAY:
                $query->andWhere(
                    [
                        'between',
                        'created_at',
                        TimeHelper::getTodayStartTime(),
                        TimeHelper::getTodayEndTime(),
                    ]
                );
                break;
            case UserScoreRuleEntity::LIMIT_TYPE_HOUR:
                $query->andWhere(
                    [
                        'between',
                        'created_at',
                        TimeHelper::getCurrentTime() - 3600,
                        TimeHelper::getCurrentTime(),
                    ]
                );
                break;
            case UserScoreRuleEntity::LIMIT_TYPE_MINUTE:
                $query->andWhere(
                    [
                        'between',
                        'created_at',
                        TimeHelper::getCurrentTime() - 60,
                        TimeHelper::getCurrentTime(),
                    ]
                );
                break;
            case UserScoreRuleEntity::LIMIT_TYPE_SECOND:
                $query->andWhere(
                    [
                        'between',
                        'created_at',
                        TimeHelper::getCurrentTime() - 1,
                        TimeHelper::getCurrentTime(),
                    ]
                );
                break;
        }

        $count = $query->count(1);

        return $count < $rule->limit_times;
    }

    /**
     * 处理用户等级变动
     */
    private function dealWithGrade()
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');
        $user = UserService::getUserById($this->id);

        $new_grade_id = $this->calculateUserGrade($user['credit'], $user['currency']);

        //调整用户等级
        if ($user['user_grade_id'] != $new_grade_id) {
            return Updater::adjustUserGrade($this->id, $new_grade_id);
        }
    }

    /**
     * 处理用户动态
     * @param CacheUserEventModel  $user_event             用户事件
     * @param UserAssociationEvent $user_association_event 关联事件：问题、回答、评论
     * @return bool
     * @throws ModelSaveErrorException
     */
    private function dealWithUserEventLog(CacheUserEventModel $user_event, UserAssociationEvent $user_association_event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');

        $model = UserEventLogEntity::find()->where(
            [
                'user_event_id'  => $user_event->id,
                'associate_type' => $user_association_event->type,
                'associate_id'   => $user_association_event->id,
            ]
        )->one();

        if (!$model) {
            $model = new UserEventLogEntity();
            $model->user_event_id = $user_event->id;
            $model->associate_type = $user_association_event->type;
            $model->associate_id = $user_association_event->id;
        }

        $model->associate_data = $user_association_event->data;

        if ($model->save()) {
            return $model->id;
        } else {
            throw new ModelSaveErrorException($model);
        }
    }

    public function goWelcome()
    {
        $url = ['/user/default/welcome'];

        return Yii::$app->getResponse()->redirect($url);
    }
}
