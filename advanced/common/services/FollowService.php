<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 14:12
 */

namespace common\services;

use common\components\Error;
use common\config\RedisKey;
use common\entities\FollowEntity;
use common\entities\FollowTagEntity;
use common\exceptions\NotFoundModelException;
use common\helpers\TimeHelper;
use common\models\AssociateModel;
use common\models\FollowQuestionModel;
use common\models\FollowTagModel;
use common\models\FollowUserModel;
use Yii;

class FollowService extends BaseService
{
    const MAX_FOLLOW_QUESTION_COUNT_BY_USING_CACHE = 5000;//当超过这个数时，关注此问题的人，不使用缓存
    const MAX_FOLLOW_TAG_COUNT_BY_USING_CACHE = 5000;//当超过这个数时，关注此标签的人，不使用缓存
    const MAX_FOLLOW_USER_COUNT_BY_USING_CACHE = 5000;//当超过这个数时，关注此用户的人，不使用缓存

    ###################### FOLLOW QUESTION ######################
    /**
     * 添加关注
     * @param $follow_question_id
     * @param $user_id
     * @return bool
     */
    public static function addFollowQuestion($follow_question_id, $user_id)
    {
        /* @var $follow_model FollowQuestionModel */
        $follow_model = Yii::createObject(FollowQuestionModel::className(), [$follow_question_id, $user_id]);

        return $follow_model->addFollow();
    }

    /**
     * 添加关注此问题的人的缓存
     * 如果此问题关注人数已超过1000，则不使用缓存
     * @param $follow_question_id
     * @param $user_id
     * @return mixed
     * @throws NotFoundModelException
     */
    public static function addUserOfFollowQuestionCache($follow_question_id, $user_id)
    {
        /* @var $follow_model FollowQuestionModel */
        $follow_model = Yii::createObject(FollowQuestionModel::className(), [$follow_question_id, $user_id]);

        return $follow_model->addFollowToCache();
    }

    /**
     * 移除关注此问题的用户
     * @param $follow_question_id
     * @param $user_id
     * @return bool|int
     * @throws NotFoundModelException
     */
    public static function removeUserOfFollowQuestionCache($follow_question_id, $user_id)
    {
        /* @var $follow_model FollowQuestionModel */
        $follow_model = Yii::createObject(FollowQuestionModel::className(), [$follow_question_id, $user_id]);

        return $follow_model->removeFollowFromCache();
    }

    /**
     * 判断用户是否已关注本问题
     * @param $follow_question_id
     * @param $user_id
     * @return bool
     * @throws NotFoundModelException
     */
    public static function checkUseIsFollowedQuestion($follow_question_id, $user_id)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        /* @var $follow_model FollowQuestionModel */
        $follow_model = Yii::createObject(FollowQuestionModel::className(), [$follow_question_id, $user_id]);

        return $follow_model->checkFollowed();
    }

    /**
     * @param      $follow_question_id
     * @param null $user_id is null, delete all follow
     * @return bool
     * @throws \Exception
     */
    public static function removeFollowQuestion($follow_question_id, $user_id = null)
    {
        /* @var $follow_model FollowQuestionModel */
        $follow_model = Yii::createObject(FollowQuestionModel::className(), [$follow_question_id, $user_id]);

        return $follow_model->removeFollow();
    }

    /**
     * 获取当前问题下关注的用户
     * @param     $question_id
     * @param int $limit
     * @return array|bool|\common\models\Follow[]
     */
    public static function getFollowQuestionUserIdsByQuestionId($question_id, $limit = 100)
    {
        $user_ids = FollowEntity::find()->select(['user_id'])->where(
            [
                'associate_type' => AssociateModel::TYPE_QUESTION,
                'associate_id'   => $question_id,
            ]
        )->limit($limit)->orderBy('created_at DESC')->column();

        return $user_ids;
    }

    public static function getFollowQuestionListByUserId($user_id, $page_no, $page_size)
    {
        $question_ids = FollowEntity::find()->select(['associate_id'])->where(
            [
                'associate_type' => AssociateModel::TYPE_QUESTION,
                'user_id'        => $user_id,
            ]
        )->limiter($page_no, $page_size)->asArray()->column();

        if ($question_ids) {
            $question_list = QuestionService::getQuestionListByQuestionIds($question_ids);
        } else {
            $question_list = [];
        }

        return $question_list;
    }

    ###################### FOLLOW TAG ######################

    public static function addFollowTag($follow_tag_id, $user_id)
    {
        /* @var $follow_model FollowTagModel */
        $follow_model = Yii::createObject(FollowTagModel::className(), [$follow_tag_id, $user_id]);

        return $follow_model->addFollow();
    }


    public static function batchAddFollowTag(array $follow_tag_ids, $user_id)
    {
        foreach ($follow_tag_ids as $tag_id) {
            self::addFollowTag($tag_id, $user_id);
        }

        return true;
    }


    public static function removeFollowTag($follow_tag_id, $user_id = null)
    {
        /* @var $follow_model FollowTagModel */
        $follow_model = Yii::createObject(FollowTagModel::className(), [$follow_tag_id, $user_id]);

        return $follow_model->removeFollow();
    }

    public static function getUserFollowTagIds($user_id, $page_no = 1, $page_size = 20)
    {
        $tag_ids = [];
        $cache_key = [RedisKey::REDIS_KEY_USER_TAG_RELATION, $user_id];

        if (0 == Yii::$app->redis->zCard($cache_key)) {
            $follow_tag_ids = FollowEntity::find()->select(['associate_id', 'created_at'])->where(
                [
                    'user_id'        => $user_id,
                    'associate_type' => AssociateModel::TYPE_TAG,
                ]
            )->orderBy('updated_at DESC')->limiter($page_no, $page_size)->asArray()->all();

            $params = [
                $cache_key,
            ];

            foreach ($follow_tag_ids as $tag) {
                $params[] = $tag['created_at'];
                $params[] = $tag['associate_id'];
                $tag_ids[] = $tag['associate_id'];
            }
            call_user_func_array([Yii::$app->redis, 'zAdd'], $params);
        } else {
            $page_no = max($page_no, 1);
            $start = ($page_no - 1) * $page_size;
            $end = ($page_size * $page_no) - 1;

            $tag_ids = Yii::$app->redis->zRevRange($cache_key, $start, $end);
        }

        return $tag_ids;
    }

    public static function getUserFollowTagList($user_id, $page_no = 1, $page_size = 20)
    {
        $tag_ids = self::getUserFollowTagIds($user_id, $page_no, $page_size);

        if ($tag_ids) {
            $tag_list = TagService::getTagListByTagIds($tag_ids);
        } else {
            $tag_list = false;
        }

        return $tag_list;
    }


    /**
     * 添加此标签的用户缓存
     * @param $follow_tag_id
     * @param $user_id
     * @return bool|mixed
     * @throws NotFoundModelException
     */
    public static function addUserOfFollowTagCache($follow_tag_id, $user_id)
    {
        /* @var $follow_model FollowTagModel */
        $follow_model = Yii::createObject(FollowTagModel::className(), [$follow_tag_id, $user_id]);

        return $follow_model->addFollowToCache();
    }

    /**
     * 移除关注此标签的用户
     * @param $follow_tag_id
     * @param $user_id
     * @return bool|int
     * @throws NotFoundModelException
     */
    public static function removeUserOfFollowTagCache($follow_tag_id, $user_id)
    {
        $tag = TagService::getTagByTagId($follow_tag_id);

        if (!$tag) {
            throw new NotFoundModelException('tag', $follow_tag_id);
        }

        $cache_key = [RedisKey::REDIS_KEY_TAG_FOLLOW_USER_LIST, $follow_tag_id];
        if (Yii::$app->redis->zScore($cache_key, $user_id) !== false) {
            //存在则移除
            return Yii::$app->redis->zRem($cache_key, $user_id);
        } else {
            return false;
        }
    }

    public static function checkUseIsFollowedTag($follow_tag_id, $user_id)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        /* @var $follow_model FollowTagModel */
        $follow_model = Yii::createObject(FollowTagModel::className(), [$follow_tag_id, $user_id]);

        return $follow_model->checkFollowed();
    }

    ###################### FOLLOW USER ######################
    public static function addFollowUser($follow_user_id, $user_id)
    {
        /* @var $follow_model FollowUserModel */
        $follow_model = Yii::createObject(FollowUserModel::className(), [$follow_user_id, $user_id]);

        return $follow_model->addFollow();
    }

    public static function batchAddFollowUser(array $follow_user_ids, $user_id)
    {
        foreach ($follow_user_ids as $follow_user_id) {
            self::addFollowUser($follow_user_id, $user_id);
        }

        return true;
    }

    /**
     * @param int  $follow_user_id
     * @param null $user_id when user_id is null, means delete user_id
     * @return bool
     * @throws \Exception
     */
    public static function removeFollowUser($follow_user_id, $user_id = null)
    {
        /* @var $follow_model FollowUserModel */
        $follow_model = Yii::createObject(FollowUserModel::className(), [$follow_user_id, $user_id]);

        return $follow_model->removeFollow();
    }

    public static function removeFollowBlog($follow_blog_id, $user_id = null)
    {
        //todo
    }

    public static function addUserFansCache($follow_user_id, $user_id)
    {
        /* @var $follow_model FollowUserModel */
        $follow_model = Yii::createObject(FollowUserModel::className(), [$follow_user_id, $user_id]);

        return $follow_model->addFollowToCache();
    }

    public static function removeUserFansCache($follow_user_id, $user_id)
    {
        /* @var $follow_model FollowUserModel */
        $follow_model = Yii::createObject(FollowUserModel::className(), [$follow_user_id, $user_id]);

        return $follow_model->removeFollowFromCache();
    }

    public static function removeUserFriendsCache($user_id, $follow_user_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_FRIEND_LIST, $user_id];
        if (Yii::$app->redis->zScore($cache_key, $follow_user_id) !== false) {
            //存在则移除
            return Yii::$app->redis->zRem($cache_key, $follow_user_id);
        } else {
            return false;
        }
    }

    public static function ensureUserFansHasCached($cache_key, $user_id)
    {
        if (Yii::$app->redis->zCard($cache_key) == 0) {
            $insert_cache_data = FollowEntity::find()->select(['created_at', 'user_id'])->where(
                [
                    'associate_id'   => $user_id,
                    'associate_type' => AssociateModel::TYPE_USER,
                ]
            )->asArray()->all();

            if ($insert_cache_data) {
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }
    }

    public static function ensureUserFriendsHasCached($cache_key, $user_id)
    {
        if (Yii::$app->redis->zCard($cache_key) == 0) {
            $insert_cache_data = FollowEntity::find()->select(['created_at', 'associate_id'])->where(
                [
                    'user_id'        => $user_id,
                    'associate_type' => AssociateModel::TYPE_USER,
                ]
            )->asArray()->all();

            if ($insert_cache_data) {
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }
    }

    public static function ensureUserOfFollowQuestionHasCached($cache_key, $follow_question_id)
    {
        if (Yii::$app->redis->zCard($cache_key) == 0) {
            $insert_cache_data = FollowEntity::find()->select(['created_at', 'user_id'])->where(
                [
                    'associate_id'   => $follow_question_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                ]
            )->asArray()->all();

            if ($insert_cache_data) {
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }
    }

    public static function checkUseIsFollowedUser($follow_user_id, $user_id)
    {
        if (Yii::$app->user->isGuest || $follow_user_id == $user_id) {
            return false;
        }

        /* @var $follow_model FollowUserModel */
        $follow_model = Yii::createObject(FollowUserModel::className(), [$follow_user_id, $user_id]);

        return $follow_model->checkFollowed();
    }

    ###################### FOLLOW TAG PASSIVE ######################

    /**
     * @param       $user_id
     * @param array $tag_ids
     * @return bool|int
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public static function addFollowTagPassive($user_id, array $tag_ids)
    {
        if (empty($user_id) || empty($tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,tag_ids']);
        }

        $data = [];
        $created_at = TimeHelper::getCurrentTime();

        foreach ($tag_ids as $tag_id) {
            $data[] = [$user_id, AssociateModel::TYPE_TAG_PASSIVE, $tag_id, $created_at, $created_at];
        }

        #batch add
        $sql = FollowEntity::getDb()->createCommand()->batchInsert(
            FollowEntity::tableName(),
            ['user_id', 'associate_type', 'associate_id', 'created_at', 'updated_at'],
            $data
        )->getSql();

        $result = FollowEntity::getDb()->createCommand(
            sprintf(
                '%s ON DUPLICATE KEY UPDATE updated_at="%d", count_follow=count_follow+1',
                $sql,
                time()
            )
        )->execute();

        if ($result === false) {
            Yii::error(sprintf('Batch Add Passive Follow Tag Error', $result), __FUNCTION__);
        }

        return $result;
    }

    /**
     * 根据标签ID获取推荐回答用户
     * @param array $tag_ids
     * @param int   $number
     * @return array ['tag_id'=>['user_id' => 'count_question', 'user_id' => 'count_question'], 'tag_id' => []]
     */
    public static function getRecommendUserIdsByTagIds(array $tag_ids, $number = 10)
    {
        $relation = [];
        foreach ($tag_ids as $tag_id) {
            $relation[$tag_id] = self::getTagAndUserRelation($tag_id);
        }
        //print_r($relation);exit;

        $total_user_number = 0;
        foreach ($relation as $item) {
            $total_user_number += count($item);
        }


        $result = [];
        foreach ($relation as $tag_id => $item) {
            $item_length = count($item);
            $split_number = round($item_length / $total_user_number * $number);

            if ($split_number < $item_length) {
                if ($split_number > 0) {
                    $result[$tag_id] = array_slice($item, 0, $split_number, true);
                }
            } else {
                $result[$tag_id] = $item;
            }
        }

        return $result;
    }

    /**
     * 通过标签与用户的关系，通过用户回答问题得到用户与标签的关系
     * @param     $tag_id
     * @param int $limit
     * @return array
     */
    public static function getTagAndUserRelation($tag_id, $limit = 12)
    {
        $cache_key = [RedisKey::REDIS_KEY_TAG_USER_RELATION, $tag_id];

        if (0 === Yii::$app->redis->zCard($cache_key)) {
            $data = FollowEntity::find()->select(
                [
                    'associate_id',
                    'user_id',
                    '`score`' => 'SUM(`count_follow`)',
                ]
            )->where(
                [
                    'associate_id'   => $tag_id,
                    'associate_type' => AssociateModel::TYPE_TAG_PASSIVE,
                ]
            )->andWhere(
                'updated_at>=:updated_at',
                [':updated_at' => TimeHelper::getBeforeTime(15)]
            )->limit($limit)->groupBy('user_id')->orderBy(
                '`score` DESC'
            )->asArray()->all();

            $relation = [];
            foreach ($data as $item) {
                $relation[$item['user_id']] = $item['score'];
            }

            if ($relation) {
                $params = [
                    $cache_key,
                ];

                foreach ($relation as $user_id => $score) {
                    $params[] = $score;
                    $params[] = $user_id;
                }

                call_user_func_array([Yii::$app->redis, 'zAdd'], $params);
            }
        }

        $result = Yii::$app->redis->zRevRange(
            $cache_key,
            0,
            -1,
            true
        );

        return $result;
    }

    /**
     * 获取一年内用户擅长的标签
     * @param     $user_id
     * @param int $limit
     * @param int $period
     * @return array|bool|string ['tag_id'=>'count_follow',]
     */
    public static function getTagIdsWhichUserIsGoodAt($user_id, $limit = 20, $period = 365)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_IS_GOOD_AT_TAG_IDS, implode(':', [$user_id, $period])];
        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $data = FollowEntity::find()->select(
                [
                    'associate_id',
                    'count_follow',
                ]
            )->where(
                [
                    'user_id'        => $user_id,
                    'associate_type' => AssociateModel::TYPE_TAG_PASSIVE,
                ]
            )->andWhere(
                'updated_at>=:updated_at',
                [':updated_at' => TimeHelper::getBeforeTime($period)]
            )->limit($limit)->orderBy(
                'count_follow DESC'
            )->asArray()->all();

            $rank = [];
            foreach ($data as $item) {
                $rank[$item['associate_id']] = $item['count_follow'];
            }

            $cache_data = $rank;

            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }

    /**
     * 获取擅长此标签的用户列表
     * 使用缓存，一天更新一次
     * @param     $tag_id
     * @param int $limit
     * @param int $period
     * @return array ['user_id' => 'count_follow', 'user_id' => 'count_follow]
     */
    public static function getUserWhichIsGoodAtThisTag($tag_id, $limit = 20, $period = 30)
    {
        $cache_key = [RedisKey::REDIS_KEY_TAG_WHICH_USER_IS_GOOD_AT, implode(':', [$tag_id, $period])];
        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $data = FollowEntity::find()->select(
                [
                    'user_id',
                    'count_follow',
                ]
            )->where(
                [
                    'associate_id'   => $tag_id,
                    'associate_type' => AssociateModel::TYPE_TAG_PASSIVE,
                ]
            )->andWhere(
                'updated_at>=:updated_at',
                [':updated_at' => TimeHelper::getBeforeTime($period)]
            )->limit($limit)->orderBy(
                'count_follow DESC'
            )->asArray()->all();
            $rank = [];
            foreach ($data as $item) {
                $rank[$item['user_id']] = $item['count_follow'];
            }

            $cache_data = $rank;
            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }


    /**
     * 获取用户粉丝
     * 需要判断，如果用户的粉丝太多，不使用缓存，每次分页查数据库
     * @param     $user_id
     * @param int $page_no
     * @param int $page_size
     * @return array
     * @throws \yii\base\Exception
     */
    public static function getUserFansUserId($user_id, $page_no = 1, $page_size = 20)
    {
        $user = UserService::getUserById($user_id);

        if ($user['count_fans'] <= self::MAX_FOLLOW_USER_COUNT_BY_USING_CACHE) {
            //使用缓存
            $cache_key = [RedisKey::REDIS_KEY_USER_FANS_LIST, $user_id];

            self::ensureUserFansHasCached($cache_key, $user_id);

            $user_ids = Yii::$app->redis->batchZRevGet($cache_key, $page_no, $page_size);
        } else {
            //直接查询数据库
            $user_ids = FollowEntity::find()->select(['user_id'])->where(
                [
                    'associate_id'   => $user_id,
                    'associate_type' => AssociateModel::TYPE_USER,
                ]
            )->orderBy('created_at DESC')->limiter($page_no, $page_size)->column();
        }

        return $user_ids;
    }

    /**
     * 获取用户关注的好友
     * @param     $user_id
     * @param int $page_no
     * @param int $page_size
     * @return array
     * @throws \yii\base\Exception
     */
    public static function getUserFriendsUserId($user_id, $page_no = 1, $page_size = 20)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_FRIEND_LIST, $user_id];

        self::ensureUserFriendsHasCached($cache_key, $user_id);

        $user_ids = Yii::$app->redis->batchZRevGet($cache_key, $page_no, $page_size);

        return $user_ids;
    }

    /**
     * @param     $user_id
     * @param int $page_no
     * @param int $page_size
     * @return array|\common\models\CacheUserModel
     */
    public static function getUserFriendsUserList($user_id, $page_no = 1, $page_size = 20)
    {
        $user_ids = self::getUserFriendsUserId($user_id, $page_no = 1, $page_size = 20);

        if ($user_ids) {
            $user_list = UserService::getUserListByIds($user_ids);
        } else {
            $user_list = [];
        }

        return $user_list;
    }
}
