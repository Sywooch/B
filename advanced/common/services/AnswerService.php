<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 16:01
 */

namespace common\services;

use common\components\Updater;
use common\config\RedisKey;
use common\entities\AnswerVersionEntity;
use common\entities\UserEntity;
use common\exceptions\ModelSaveErrorException;
use common\exceptions\NotFoundModelException;
use common\helpers\ArrayHelper;
use common\models\AssociateModel;
use common\models\CacheAnswerModel;
use yii\helpers\Url;
use common\components\Judger;
use common\entities\AnswerEntity;
use Yii;

class AnswerService extends BaseService
{
    

    public function agreeAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
        }
    }

    public function cancelAgreeAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
        }
    }

    public function addComment($answer_id, $user_id, $content, $is_anonymous)
    {
        CommentService::addAnswerComment($answer_id, $user_id, $content, $is_anonymous);
    }


    public static function foldAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            /* @var $model AnswerEntity */
            $model = AnswerEntity::findOne(['answer_id' => $answer_id]);
            if ($model) {
                $model->is_fold = AnswerEntity::STATUS_FOLD;
                $model->save();
            }
        }
    }

    public static function cancelFoldAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            /* @var $model AnswerEntity */
            $model = AnswerEntity::findOne(['answer_id' => $answer_id]);
            if ($model) {
                $model->is_fold = AnswerEntity::STATUS_UNFOLD;
                $model->save();
            }
        }
    }

    public static function anonymousAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            /* @var $model AnswerEntity */
            $model = AnswerEntity::findOne(['answer_id' => $answer_id]);
            if ($model) {
                $model->is_anonymous = AnswerEntity::STATUS_ANONYMOUS;
                $model->save();
            }
        }
    }

    public static function cancelAnonymousAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            /* @var $model AnswerEntity */
            $model = AnswerEntity::findOne(['answer_id' => $answer_id]);
            if ($model) {
                $model->is_anonymous = AnswerEntity::STATUS_UNANONYMOUS;
                $model->save();
            }
        }
    }

    public static function findAtUsers($content)
    {
        preg_match_all("/(\S*)\@([^\r\n\s]*)/i", $content, $at_list_tmp);
        $users = [];
        foreach ($at_list_tmp[2] as $key => $value) {
            if ($at_list_tmp[1][$key] || strlen($value) > 25) {
                continue;
            }
            $users[] = $value;
        }

        return ArrayHelper::map(UserEntity::find()->where(['username' => $users])->all(), 'id', 'username');
    }

    public static function replace($content)
    {
        preg_match_all("/\#(\d*)/i", $content, $floor);
        if (isset($floor[1])) {
            foreach ($floor[1] as $key => $value) {
                $search = "#{$value}楼";
                $place = "[{$search}](#comment{$value}) ";
                $content = str_replace($search . ' ', $place, $content);
            }
        }

        $users = self::findAtUsers($content);
        foreach ($users as $key => $value) {
            $search = '@' . $value;
            $url = Url::to(['/user/default/show', 'username' => $value]);
            $place = "[{$search}]({$url}) ";
            $content = str_replace($search . ' ', $place, $content);
        }

        return $content;
    }

    public static function getAnswerUserIdsByQuestionId($question_id, $limit = 100)
    {
        $sql = "SELECT
                  GROUP_CONCAT(
                    CONCAT(
                      a.`created_by`,
                      ',',
                      c.`created_by`
                    )
                  ) as user_ids
                FROM
                  `answer` a
                  LEFT JOIN `comment` c
                    ON a.`id` = c.`associate_id`
                WHERE a.`question_id` =:question_id
                AND a.is_anonymous=:not_anonymous
                AND c.is_anonymous=:not_anonymous
                AND c.associate_type=:associate_type
                ORDER BY a.`created_at` DESC, c.`created_at` DESC
                LIMIT :limit ;
                ";

        $command = AnswerEntity::getDb()->createCommand(
            $sql,
            [
                ':not_anonymous'  => AnswerEntity::STATUS_UNANONYMOUS,
                ':question_id'    => $question_id,
                ':associate_type' => AssociateModel::TYPE_ANSWER_COMMENT,
                ':limit'          => $limit,
            ]
        );

        $data = $command->queryScalar();

        if ($data) {
            $data = array_unique(explode(',', $data));
        }

        return $data;
    }

    /**
     * 判断用户是否回答过本题，
     * @param $question_id
     * @param $user_id
     * @return bool
     */
    public static function checkWhetherHasAnswered($question_id, $user_id)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $cache_key = [RedisKey::REDIS_KEY_QUESTION_HAS_ANSWERED, implode(':', [$user_id, $question_id])];

        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $cache_data = AnswerEntity::find()->select('id')->where(
                [
                    'question_id' => $question_id,
                    'created_by'  => $user_id,
                ]
            )->scalar();

            //如果没有回答过，则设置answer_id=0
            if (!$cache_data) {
                $cache_data = 0;
            }
            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data > 0;
    }

    public static function getAnswerCountByQuestionId($question_id)
    {
        $question_data = QuestionService::getQuestionByQuestionId($question_id);
        if (false !== $question_data) {
            $count = $question_data['count_answer'];
        } else {
            $count = 0;
        }

        return $count;
    }

    private static function getAnswerListByQuestionIdOrderByTime($question_id, $page_no = 1, $page_size = 10)
    {
        $cache_key = [RedisKey::REDIS_KEY_ANSWER_LIST_TIME, $question_id];
        #answer_count
        if (self::getAnswerCountByQuestionId($question_id) > 0) {
            if (0 == Yii::$app->redis->zCard($cache_key)) {
                self::setAnswerListByQuestionIdOrderToCache($question_id);
            }
            //zrevrange score 0 -1 withscores
            $page_no = max($page_no, 1);
            $start = ($page_no - 1) * $page_size;
            $end = $page_size * $page_no - 1;

            $answer_ids = Yii::$app->redis->zRevRange($cache_key, $start, $end);
        } else {
            $answer_ids = [];
        }

        if ($answer_ids) {
            $data = self::getAnswerListByAnswerId($answer_ids);
        } else {
            $data = [];
        }

        return $data;
    }

    private static function getAnswerListByQuestionIdOrderByScore($question_id, $page_no = 1, $page_size = 10)
    {
        $cache_key = [RedisKey::REDIS_KEY_ANSWER_LIST_SCORE, $question_id];
        $answer_count = self::getAnswerCountByQuestionId($question_id);

        if ($answer_count > 0) {
            if (0 == Yii::$app->redis->zCard($cache_key)) {
                self::setAnswerListByQuestionIdOrderToCache($question_id);
            }

            $page_no = max($page_no, 1);
            $start = ($page_no - 1) * $page_size;
            $end = ($page_size * $page_no) - 1;

            $answer_ids = Yii::$app->redis->zRevRange($cache_key, $start, $end);
        } else {
            $answer_ids = [];
        }

        if ($answer_ids) {
            $data = self::getAnswerListByAnswerId($answer_ids);
        } else {
            $data = [];
        }

        return $data;
    }

    private static function setAnswerListByQuestionIdOrderToCache($question_id)
    {
        $answer_data = AnswerEntity::find()->select(
            [
                'id',
                'count_useful' => '`count_like`-`count_hate`',
                'created_at',
            ]
        )->where(
            ['question_id' => $question_id]
        )->asArray()->all();


        $score_params = [
            [RedisKey::REDIS_KEY_ANSWER_LIST_SCORE, $question_id],
        ];

        foreach ($answer_data as $item) {
            $score_params[] = $item['count_useful'];
            $score_params[] = $item['id'];
        }
        call_user_func_array([Yii::$app->redis, 'zAdd'], $score_params);


        #add to time list
        $time_params = [
            [RedisKey::REDIS_KEY_ANSWER_LIST_TIME, $question_id],
        ];

        foreach ($answer_data as $item) {
            $time_params[] = $item['created_at'];
            $time_params[] = $item['id'];
        }
        call_user_func_array([Yii::$app->redis, 'zAdd'], $time_params);
    }

    /**
     * @param $answer_id
     * @return CacheAnswerModel
     * @throws NotFoundModelException
     */
    public static function getAnswerByAnswerId($answer_id)
    {
        $data = self::getAnswerListByAnswerId([$answer_id]);
        if ($data) {
            return array_shift($data);
        } else {
            throw new NotFoundModelException('answer', $answer_id);
        }
    }


    /**
     * @param array $answer_ids
     * @return array|CacheAnswerModel
     */
    public static function getAnswerListByAnswerId(array $answer_ids)
    {
        $cache_miss_key = $result = [];
        $cache_answer_model = new CacheAnswerModel();

        foreach ($answer_ids as $answer_id) {
            $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
            $cache_data = Yii::$app->redis->hGetAll($cache_key);

            if (empty($cache_data)) {
                $cache_miss_key[] = $answer_id;
                $result[$answer_id] = null;
            } else {
                $result[$answer_id] = $cache_answer_model->build($cache_data);
            }
        }

        if ($cache_miss_key) {
            $query = AnswerEntity::find();
            $data = $query->where(['id' => $cache_miss_key])->asArray()->all();


            foreach ($data as $item) {
                $answer_id = $item['id'];
                #filter attributes
                $data = $cache_answer_model->filter($item);
                $result[$answer_id] = $cache_answer_model->build($data);
                $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
                Yii::$app->redis->hMset($cache_key, $data);
            }
        }

        return $result;
    }

    public static function getAnswerListByQuestionId($question_id, $page_no, $page_size, $sort = 'default')
    {
        switch ($sort) {
            case 'created':
                $data = self::getAnswerListByQuestionIdOrderByTime($question_id, $page_no, $page_size);
                break;

            case 'default':
                $data = self::getAnswerListByQuestionIdOrderByScore($question_id, $page_no, $page_size);
                break;
            default:
                $data = [];
        }

        return $data;
    }


    public static function ensureAnswerHasCache($answer_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
        if (Yii::$app->redis->hLen($cache_key) == 0) {
            return self::getAnswerByAnswerId($answer_id);
        }

        return true;
    }

    public static function deleteAnswerCache($answer_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];

        return Yii::$app->redis->delete($cache_key);
    }

    /**
     * 更新回答缓存
     * @param $answer_id
     * @param $data
     * @return bool
     */
    public static function updateAnswerCache($answer_id, $data)
    {
        $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
        if ($answer_id && $data && Yii::$app->redis->hLen($cache_key)) {
            return Yii::$app->redis->hMset($cache_key, $data);
        }

        return true;
    }


    /**
     * @param $user_id
     * @param $page_no
     * @param $page_size
     * @return array|bool|CacheAnswerModel
     */
    public static function getAnswerListByUserId($user_id, $page_no, $page_size)
    {
        $limit = $page_size;
        $offset = max($page_no - 1, 0) * $page_size;

        $query = AnswerEntity::find()->select(
            [
                'id',
            ]
        )->where(
            [
                'created_by' => $user_id,
            ]
        )->orderBy('updated_at DESC, created_at DESC')->limit($limit)->offset(
            $offset
        );

        //do not show anonymous answer
        $show_anonymous = UserService::checkUserSelf($user_id);
        if (!$show_anonymous) {
            $query->andWhere(['is_anonymous' => AnswerEntity::STATUS_UNANONYMOUS]);
        }

        $answer_ids = $query->column();

        if ($answer_ids) {
            $answer_list = AnswerService::getAnswerListByAnswerId($answer_ids);
        } else {
            $answer_list = false;
        }

        return $answer_list;
    }

    /**
     * @param $user_id
     * @param $page_no
     * @param $page_size
     * @return array|bool|\common\models\CacheQuestionModel
     */
    public static function getAnsweredQuestionListByUserId($user_id, $page_no, $page_size)
    {
        $limit = $page_size;
        $offset = max($page_no - 1, 0) * $page_size;

        $query = AnswerEntity::find()->select(
            [
                'question_id',
            ]
        )->where(
            [
                'created_by' => $user_id,
            ]
        )->orderBy('updated_at DESC, created_at DESC')->limit($limit)->offset(
            $offset
        );

        //do not show anonymous answer
        $show_anonymous = UserService::checkUserSelf($user_id);
        if (!$show_anonymous) {
            $query->andWhere(['is_anonymous' => AnswerEntity::STATUS_UNANONYMOUS]);
        }

        $question_ids = $query->column();

        if ($question_ids) {
            $question_list = QuestionService::getQuestionListByQuestionIds($question_ids);
        } else {
            $question_list = false;
        }

        return $question_list;
    }

    // answer version


    public static function addNewVersion($answer_id, $content, $reason)
    {
        if (self::ensureExistTheFirstEdition($answer_id) === false) {
            return Error::set(Error::TYPE_ANSWER_ENSURE_EXIST_THE_FIRST_EDITION);
        }

        $model = new AnswerVersionEntity;
        $data = [
            'answer_id' => $answer_id,
            'content'   => $content,
            'reason'    => $reason,
        ];

        if ($model->load($data, '') && $model->save()) {
            $result = true;
        } else {
            throw new ModelSaveErrorException($model);
        }

        return $result;
    }

    private static function ensureExistTheFirstEdition($answer_id)
    {
        $result = false;
        if (!AnswerVersionEntity::findOne(['answer_id' => $answer_id])) {
            /* @var $answer AnswerEntity */
            $answer = AnswerEntity::findOne(['id' => $answer_id]);
            if ($answer) {
                $model = new AnswerVersionEntity;
                $data = [
                    'answer_id'  => $answer->id,
                    'content'    => $answer->content,
                    'reason'     => null,
                    'created_by' => $answer->created_by,
                    'created_at' => $answer->created_at,
                ];
                if ($model->load($data, '') && $model->save()) {
                    $result = true;
                } else {
                    throw new ModelSaveErrorException($model);
                }
            }
        } else {
            $result = true;
        }

        return $result;
    }

    public static function getAnswerVersionList($answer_id, $limit = 10, $offset = 0)
    {
        return AnswerVersionEntity::find()->where(
            [
                'answer_id' => $answer_id,
            ]
        )->limit($limit)->offset($offset)->orderBy('id DESC')->asArray()->all();
    }


    public static function setAnonymous($answer_id)
    {
        return Updater::setAnswerAnonymous($answer_id);
    }

    public static function cancelAnonymous($answer_id)
    {
        return Updater::cancelAnswerAnonymous($answer_id);
    }
}
