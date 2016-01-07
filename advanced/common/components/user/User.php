<?php
/**
 * 用户模型，实例：Yii::$app->user
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/3
 * Time: 20:07
 */

namespace common\components\user;

use common\components\Error;
use common\components\Updater;
use common\config\RedisKey;
use common\entities\UserEventEntity;
use common\entities\UserFeedEntity;
use common\entities\UserGradeRuleEntity;
use common\entities\UserScoreLogEntity;
use common\entities\UserScoreRuleEntity;
use common\exceptions\UserEventDoesNotDefineException;
use common\helpers\TimeHelper;
use common\models\CacheUserScoreRuleModel;
use common\models\CacheUserEventModel;
use common\models\CacheUserGradeModel;
use common\services\UserService;
use yii\base\Event;
use yii\helpers\Inflector;
use Yii;

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

    private function getAllCreditRule()
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_CREDIT_RULE];
        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $rules = UserScoreRuleEntity::findAll(['status' => UserScoreRuleEntity::STATUS_ENABLE]);
            $data = [];

            foreach ($rules as $rule) {
                $data[$rule->user_event_id] = (new CacheUserScoreRuleModel())->filterAttributes($rule);
            }

            $cache_data = $data;
            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }

    /**
     * 获取积分规则
     * @param $event_id
     * @return CacheUserScoreRuleModel
     */
    private function getCreditRule($event_id)
    {
        $rules = $this->getAllCreditRule();
        if (isset($rules[$event_id])) {
            return $rules[$event_id];
        } else {
            return false;
        }
    }

    /**
     * @return array|CacheUserEventModel
     */
    private function getAllUserEvents()
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_EVENT_LIST];
        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $rules = UserEventEntity::findAll(['status' => UserEventEntity::STATUS_ENABLE]);
            $data = [];

            foreach ($rules as $rule) {
                $data[$rule->event] = (new CacheUserEventModel())->filterAttributes($rule);
            }

            $cache_data = $data;
            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }

    /**
     * 根据事件名称获取事件
     * @param $event_name
     * @return CacheUserEventModel
     */
    private function getUserEventByEventName($event_name)
    {
        $events = $this->getAllUserEvents();

        if (isset($events[$event_name])) {
            return $events[$event_name];
        } else {
            return false;
        }
    }

    private function getAllUserGrades()
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_GRADE_RULE];
        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $rules = UserGradeRuleEntity::find()->where(
                ['status' => UserGradeRuleEntity::STATUS_ENABLE]
            )->orderBy('credit ASC')->all();
            $data = [];

            foreach ($rules as $rule) {
                $data[$rule->id] = (new CacheUserGradeModel())->filterAttributes($rule);
            }

            $cache_data = $data;
            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }

    private function calculateUserGrade()
    {
        $creditRule = '';
    }

    /**
     * 用户事件触发，不得以 before,after开头，YII框架级别的用户事件，使用before,after开头
     * @param string     $name
     * @param Event|null $event
     * @throws UserEventDoesNotDefineException
     */
    public function trigger($name, Event $event = null)
    {
        //触发框架级用户事件
        if (strpos($name, 'before') === 0 || strpos($name, 'after') === 0) {
            return parent::trigger($name, $event);
        } else {
            $event_name = Inflector::underscore($name);

            //检查事件是否存在
            if ($user_event = $this->getUserEventByEventName($event_name)) {
                //用户动态
                $this->dealWithFeed($user_event, $event);
                //用户积分
                $this->dealWithCredit($user_event->id);
            } else {
                throw new UserEventDoesNotDefineException($event_name);
            }
        }
    }

    /**
     * 处理用户积分变动
     * @param $event_id
     * @return mixed
     */
    private function dealWithCredit($event_id)
    {
        $rule = $this->getCreditRule($event_id);
        if ($rule && $this->checkIfCanExecuteScoreRule($rule)) {
            if (Updater::updateUserScore($this->id, $rule->type, $rule->score)) {
                //积分日志
                $this->dealWithUserScoreLog($rule);
                //用户等级
                $this->dealWithGrade();

                return true;
            }
        }

        return false;
    }

    private function dealWithUserScoreLog(CacheUserScoreRuleModel $rule)
    {
        $model = new UserScoreLogEntity();
        $data = [
            'user_event_id' => $rule->user_event_id,
            'type'          => $rule->type,
            'score'         => $rule->score,
            'created_by'    => $this->id,
            'created_at'    => TimeHelper::getCurrentTime(),
        ];

        if ($model->load($data, '') && $model->save()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 判断当前规则是否可以执行
     * 查看数据库，判断是否超过限制
     * @param CacheUserScoreRuleModel $rule
     * @return bool
     */
    private function checkIfCanExecuteScoreRule(CacheUserScoreRuleModel $rule)
    {
        if ($rule->limit_type == UserScoreRuleEntity::LIMIT_TYPE_LIMITLESS) {
            return true;
        }
        $query = UserScoreLogEntity::find()->where(
            [
                'user_event_id' => $rule->user_event_id,
                'created_by'    => $this->id,
            ]
        );
        switch ($rule->limit_type) {
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

        return $count < $rule->limit_interval;
    }

    /**
     * 处理用户等级变动
     */
    private function dealWithGrade()
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');
        $user = UserService::getUserById($this->id);


    }

    /**
     * 处理用户动态
     * @param CacheUserEventModel  $user_event             用户事件
     * @param UserAssociationEvent $user_association_event 关联事件：问题、回答、评论
     * @return bool
     */
    private function dealWithFeed(CacheUserEventModel $user_event, UserAssociationEvent $user_association_event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');

        //不需要记录FEED
        if ($user_event->record == UserEventEntity::RECORD_NO) {
            return false;
        }

        $model = UserFeedEntity::find()->where(
            [
                'user_event_id'  => $user_event->id,
                'associate_type' => $user_association_event->type,
                'associate_id'   => $user_association_event->id,
            ]
        )->one();

        if (!$model) {
            $model = new UserFeedEntity();
            $model->user_event_id = $user_event->id;
            $model->associate_type = $user_association_event->type;
            $model->associate_id = $user_association_event->id;
            $model->associate_content = $user_association_event->content;
        }

        $model->created_at = TimeHelper::getCurrentTime();

        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }
}
