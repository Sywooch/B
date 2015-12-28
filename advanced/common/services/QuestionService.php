<?php

namespace common\services;

use common\components\Error;
use common\config\RedisKey;
use common\entities\QuestionTagEntity;
use common\models\CacheQuestionModel;
use common\models\QuestionTag;
use common\models\xunsearch\QuestionSearch;
use XSException;
use yii\base\Exception;
use common\entities\QuestionEntity;
use Yii;

class QuestionService extends BaseService
{
    public static function getQuestionByQuestionId($question_id)
    {
        $data = self::getQuestionListByQuestionIds([$question_id]);

        return $data ? array_shift($data) : false;
    }

    public static function getQuestionListByQuestionIds(array $question_ids)
    {
        $result = $cache_miss_key = $cache_data = [];
        foreach ($question_ids as $question_id) {
            $cache_key = [RedisKey::REDIS_KEY_QUESTION, $question_id];
            $cache_data = Yii::$app->redis->hGetAll($cache_key);

            if (empty($cache_data)) {
                $cache_miss_key[] = $question_id;
                $result[$question_id] = null;
            } else {
                $result[$question_id] = $cache_data;
            }
        }
        if ($cache_miss_key) {
            $cache_data = QuestionEntity::find()->where(
                [
                    'id' => $cache_miss_key,
                ]
            )->asArray()->all();

            $cache_question_model = new CacheQuestionModel();
            foreach ($cache_data as $item) {
                #filter attributes
                $item = $cache_question_model->filterAttributes($item);

                $question_id = $item['id'];
                $result[$question_id] = $item;
                $cache_key = [RedisKey::REDIS_KEY_QUESTION, $question_id];
                Yii::$app->redis->hMset($cache_key, $item);
            }
        }

        return $result;
    }

    /**
     * @param $question_id
     * @return $this
     */
    public static function getQuestionTagsByQuestionId($question_id)
    {
        $sql = sprintf(
            'select t.id, t.name
                from `%s` qt
                left join `tag` t
                on t.id=qt.tag_id
                where qt.question_id=:question_id
                ',
            QuestionTag::tableName()
        );

        return QuestionEntity::getDb()->createCommand($sql, [':question_id' => $question_id])->queryAll();
    }

    /**
     * 更新问题缓存
     * @param $question_id
     * @param $data
     * @return bool
     */
    public static function updateQuestionCache($question_id, $data)
    {
        $cache_key = [RedisKey::REDIS_KEY_QUESTION, $question_id];
        if ($question_id && $data && Yii::$app->redis->hLen($cache_key)) {
            return Yii::$app->redis->hMset($cache_key, $data);
        }

        return false;
    }

    public static function fetchCount($type, $is_spider)
    {
        switch ($type) {
            case 'latest':
                $count = QuestionEntity::find()->allowShowStatus($is_spider)->orderByTime()->count(1);
                break;

            case 'hottest':
                $count = QuestionEntity::find()->allowShowStatus($is_spider)->recent()->answered()->orderByTime(
                )->count(1);
                break;

            case 'unAnswer':
                $count = QuestionEntity::find()->allowShowStatus($is_spider)->unAnswered()->orderByTime()->count(1);
                break;

            default:
                $count = 0;
        }

        #总数不得超过 1000
        return min($count, 1000);
    }

    public static function fetchLatest($limit = 10, $offset = 0, $is_spider = false)
    {
        $cache_key = [
            RedisKey::REDIS_KEY_QUESTION_BLOCK,
            implode(
                '_',
                [
                    'LATEST',
                    $limit,
                    $offset,
                    $is_spider,
                ]
            ),
        ];

        $question_ids = Yii::$app->redis->get($cache_key);

        if ($question_ids === false) {
            $model = QuestionEntity::find()->select(['id'])->allowShowStatus($is_spider)->orderByTime()->limit(
                $limit
            )->offset(
                $offset
            )->column();
            $question_ids = $model;

            Yii::$app->redis->set($cache_key, $question_ids);
        }

        if ($question_ids) {
            $result = QuestionService::getQuestionListByQuestionIds($question_ids);
        } else {
            $result = [];
        }

        return $result;
    }

    public static function fetchHot($limit = 10, $offset = 0, $is_spider = false, $period = 7)
    {
        $cache_key = [
            RedisKey::REDIS_KEY_QUESTION_BLOCK,
            implode(
                '_',
                [
                    'HOT',
                    $limit,
                    $offset,
                    $period,
                    $is_spider,
                ]
            ),
        ];

        $question_ids = Yii::$app->redis->get($cache_key);

        if ($question_ids === false) {
            $model = QuestionEntity::find()->select(['id'])->answered(3)->allowShowStatus($is_spider)->recent($period)->answered(
            )->orderByTime()->limit(
                $limit
            )->offset($offset)->column();

            $question_ids = $model;
            Yii::$app->redis->set($cache_key, $question_ids);
        }

        if ($question_ids) {
            $result = QuestionService::getQuestionListByQuestionIds($question_ids);
        } else {
            $result = [];
        }

        return $result;
    }

    public static function fetchUnAnswer($limit = 10, $offset = 0, $is_spider = false, $period = 30)
    {
        $cache_key = [
            RedisKey::REDIS_KEY_QUESTION_BLOCK,
            implode(
                '_',
                [
                    'UNANSWER',
                    $limit,
                    $offset,
                    $period,
                    $is_spider,
                ]
            ),
        ];

        $question_ids = Yii::$app->redis->get($cache_key);

        if ($question_ids === false) {
            $model = QuestionEntity::find()->select(['id'])->allowShowStatus($is_spider)->recent($period)->unAnswered()->orderByTime(
            )->limit($limit)->offset($offset)->column();
            $question_ids = $model;
            Yii::$app->redis->set($cache_key, $question_ids);
        }

        if ($question_ids) {
            $result = QuestionService::getQuestionListByQuestionIds($question_ids);
        } else {
            $result = [];
        }

        return $result;
    }

    public static function getSubjectTags($subject, $limit = 5)
    {
        $question = new QuestionSearch();
        $tags = $question->fenci($subject, $limit);

        return $tags;
    }

    public static function searchQuestionByTag(array $tags, $limit = 10)
    {
        $cache_data = [];
        if ($tags) {
            $params = array_merge(['or'], array_unique($tags));
            try {
                $cache_key = [RedisKey::REDIS_KEY_XUNSEARCH_TAG, md5(implode(':', $tags) . $limit)];
                $cache_data = Yii::$app->redis->get($cache_key);

                if ($cache_data === false) {
                    $cache_data = QuestionSearch::find()->where($params)->andWhere(
                        [
                            'NOT IN',
                            'count_answer',
                            [0],
                        ]
                    )->limit(
                        $limit
                    )->orderBy('created_at DESC')->asArray()->all();
                    Yii::$app->redis->set($cache_key, $cache_data);
                }

            } catch (Exception $e) {
                return Error::set(Error::TYPE_QUESTION_XUNSEARCH_GET_EXCEPTION, [$e->getCode(), $e->getMessage()]);
            }
        }

        return $cache_data;
    }

    public static function searchQuestionBySubject($subject, $limit = 10)
    {
        $result = [];

        if ($subject) {
            $tags = self::getSubjectTags($subject);
            if ($tags === false) {
                return false;
            } else {
                $result = self::searchQuestionByTag($tags, $limit);
            }
        }

        return $result;
    }

    public static function getInterestedQuestionByUserId($user_id, $limit = 50)
    {
        $tag_ids = FollowService::getUserFollowTagIds($user_id);
        $result = [];
        if ($tag_ids) {
            $tag_names = TagService::getTagNameById($tag_ids);
            $result = self::searchQuestionByTag($tag_names, $limit);
        }

        return $result;
    }

    public static function ensureQuestionHasCache($question_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_QUESTION, $question_id];
        if (Yii::$app->redis->hLen($cache_key) == 0) {
            return self::getQuestionByQuestionId($question_id);
        }

        return true;
    }

    public static function getQuestionListByUserId($user_id, $page_no = 1, $page_size = 10)
    {
        $limit = $page_size;
        $offset = max($page_no - 1, 0) * $page_size;


        $query = QuestionEntity::find()->select(
            [
                'id',
            ]
        )->where(
            ['created_by' => $user_id]
        )->orderBy('updated_at DESC, created_at DESC')->limit($limit)->offset($offset);


        //do not show anonymous answer
        $show_anonymous = UserService::checkUserSelf($user_id);
        if (!$show_anonymous) {
            $query->andWhere(['is_anonymous' => QuestionEntity::STATUS_UNANONYMOUS]);
        }

        $question_ids = $query->column();
        if ($question_ids) {
            $question_list = QuestionService::getQuestionListByQuestionIds($question_ids);
        } else {
            $question_list = false;
        }

        return $question_list;
    }

    public static function getQuestionListByTagId($tag_id, $page_no = 1, $page_size = 10)
    {
        $query = QuestionTagEntity::find()->select(
            [
                'question_id',
            ]
        )->where(
            ['tag_id' => $tag_id]
        )->orderBy('created_at DESC')->limiter($page_no, $page_size, 200);

        $question_ids = $query->column();

        if ($question_ids) {
            $question_list = QuestionService::getQuestionListByQuestionIds($question_ids);
        } else {
            $question_list = [];
        }

        return $question_list;
    }

    public static function getQuestionCountByTagId($tag_id)
    {
        $tag = TagService::getTagByTagId($tag_id);
        if ($tag) {
            $count = $tag['count_use'];
        } else {
            $count = 0;
        }

        return $count;
    }

    /**
     * add question tag
     * @param       $user_id
     * @param       $question_id
     * @param array $tag_ids
     * @return bool
     */
    public static function addQuestionTag($user_id, $question_id, array $tag_ids)
    {
        if (empty($user_id) || empty($question_id) || empty($tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,question_id,tag_ids']);
        }

        $data = [];
        $created_at = time();

        foreach ($tag_ids as $tag_id) {
            $model = new QuestionTagEntity();
            $model->question_id = $question_id;
            $model->tag_id = $tag_id;
            $model->created_by = $user_id;
            $model->save();
        }

        return true;
        /*foreach ($tag_ids as $tag_id) {
            $data[] = [$question_id, $tag_id, $user_id, $created_at];
        }

        #batch add question tag
        $result = QuestionTagEntity::getDb()->createCommand()->batchInsert(
            QuestionTag::tableName(),
            ['question_id', 'tag_id', 'created_by', 'created_at'],
            $data
        )->execute();

        #add follow tag
        if ($result) {
            #add user follow tag
            FollowService::batchAddFollowTag($tag_ids, $user_id);
            #tag use count
            TagService::updateTagCountUse($tag_ids);
        }*/


    }

}
