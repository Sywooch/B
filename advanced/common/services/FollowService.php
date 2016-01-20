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
use common\entities\FollowQuestionEntity;
use common\entities\FollowTagEntity;
use common\entities\FollowTagPassiveEntity;
use common\entities\FollowUserEntity;
use common\exceptions\ModelSaveErrorException;
use common\exceptions\NotFoundModelException;
use common\helpers\TimeHelper;
use common\models\CacheUserModel;
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
        Yii::trace(sprintf('用户%d关注问题%d', $user_id, $follow_question_id), 'service');

        if (empty($user_id) || empty($follow_question_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,follow_question_id']);
        }

        $user = UserService::getUserById($user_id);

        if ($user['count_follow_question'] > FollowQuestionEntity::MAX_FOLLOW_QUESTION_NUMBER) {
            return Error::set(
                Error::TYPE_FOLLOW_QUESTION_FOLLOW_TOO_MUCH_QUESTION,
                FollowQuestionEntity::MAX_FOLLOW_QUESTION_NUMBER
            );
        }

        if (!FollowQuestionEntity::findOne(
            [
                'user_id'            => $user_id,
                'follow_question_id' => $follow_question_id,
            ]
        )
        ) {
            $model = new FollowQuestionEntity;
            $model->user_id = $user_id;
            $model->follow_question_id = $follow_question_id;
            if ($model->save()) {
                $result = true;
            } else {
                throw new ModelSaveErrorException($model);
            }
        } else {
            $result = true;
        }

        Yii::trace(sprintf('关注结果 %s', var_export($result, true)), 'service');

        return $result;
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
        $question = QuestionService::getQuestionByQuestionId($follow_question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $follow_question_id);
        }

        $cache_key = [RedisKey::REDIS_KEY_QUESTION_FOLLOW_USER_LIST, $follow_question_id];

        if ($question['count_follow'] < self::MAX_FOLLOW_QUESTION_COUNT_BY_USING_CACHE) {
            self::ensureUserOfFollowQuestionHasCached($cache_key, $follow_question_id);

            $insert_cache_data = [];
            //存在则判断是否已存在集合中
            $cache_data = Yii::$app->redis->zScore($cache_key, $user_id);

            if ($cache_data === false) {
                $insert_cache_data[] = ['create_at' => TimeHelper::getCurrentTime(), 'user_id' => $user_id];
            }

            if ($insert_cache_data) {
                //添加到缓存中
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }

        return false;
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
        $question = QuestionService::getQuestionByQuestionId($follow_question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $follow_question_id);
        }

        $cache_key = [RedisKey::REDIS_KEY_QUESTION_FOLLOW_USER_LIST, $follow_question_id];
        if (Yii::$app->redis->zScore($cache_key, $user_id) !== false) {
            //存在则移除
            return Yii::$app->redis->zRem($cache_key, $user_id);
        } else {
            return false;
        }
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

        $question = QuestionService::getQuestionByQuestionId($follow_question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $follow_question_id);
        }

        $cache_key = [RedisKey::REDIS_KEY_QUESTION_FOLLOW_USER_LIST, $follow_question_id];

        if ($question['count_follow'] < self::MAX_FOLLOW_QUESTION_COUNT_BY_USING_CACHE) {
            self::ensureUserOfFollowQuestionHasCached($cache_key, $follow_question_id);

            //小于1000，则使用缓存
            $result = Yii::$app->redis->zScore($cache_key, $user_id);
        } else {
            //大于则查询数据库
            $result = FollowQuestionEntity::find()->where(
                [
                    'user_id'            => $user_id,
                    'follow_question_id' => $follow_question_id,
                ]
            )->count(1);
        }

        return (bool)$result;
    }

    /**
     * @param      $follow_question_id
     * @param null $user_id is null, delete all follow
     * @return bool
     * @throws \Exception
     */
    public static function removeFollowQuestion($follow_question_id, $user_id = null)
    {
        if (empty($follow_question_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,follow_question_id']);
        }

        #delete
        /* @var $model FollowQuestionEntity */
        $model = FollowQuestionEntity::find()->where(
            [
                'follow_question_id' => $follow_question_id,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        foreach ($model as $follow_question) {
            //移除数据库数据
            $follow_question->delete();
        }

        return true;
    }

    /**
     * 获取当前问题下关注的用户
     * @param     $follow_question_id
     * @param int $limit
     * @return array|bool|\common\models\FollowQuestion[]
     */
    public static function getFollowQuestionUserIdsByQuestionId($follow_question_id, $limit = 100)
    {
        $user_ids = FollowQuestionEntity::find()->select('user_id')->where(
            [
                'follow_question_id' => $follow_question_id,
            ]
        )->limit($limit)->orderBy('created_at DESC')->column();

        return $user_ids;
    }

    public static function getFollowQuestionListByUserId($user_id, $page_no, $page_size)
    {
        $question_ids = FollowQuestionEntity::find()->select(['follow_question_id'])->where(
            [
                'user_id' => $user_id,
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
        Yii::trace(sprintf('用户%d关注标签%d', $user_id, $follow_tag_id), 'service');

        if (empty($user_id) || empty($follow_tag_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,follow_tag_id']);
        }

        $user = UserService::getUserById($user_id);

        if ($user['count_follow_tag'] > FollowTagEntity::MAX_FOLLOW_TAG_NUMBER) {
            return Error::set(
                Error::TYPE_FOLLOW_TAG_FOLLOW_TOO_MUCH_TAG,
                FollowTagEntity::MAX_FOLLOW_TAG_NUMBER
            );
        }

        if (!FollowTagEntity::findOne(
            [
                'follow_tag_id' => $follow_tag_id,
                'user_id'       => $user_id,
            ]
        )
        ) {
            $model = new FollowTagEntity();
            $model->user_id = $user_id;
            $model->follow_tag_id = $follow_tag_id;
            if ($model->save()) {
                $result = true;
            } else {
                throw new ModelSaveErrorException($model);
            }
        } else {
            $result = true;
        }

        Yii::trace(sprintf('关注结果 %s', var_export($result, true)), 'service');

        return $result;
    }


    public static function batchAddFollowTag(array $follow_tag_ids, $user_id)
    {
        foreach ($follow_tag_ids as $tag_id) {
            self::addFollowTag($tag_id, $user_id);
        }

        return true;
    }

    /*public static function batchAddFollowTag(array $tag_ids, $user_id)
    {
        if (empty($user_id) || empty($tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,tag_ids']);
        }

        $data = [];
        $created_at = TimeHelper::getCurrentTime();

        foreach ($tag_ids as $tag_id) {
            $data[] = [$user_id, $tag_id, $created_at, $created_at];
        }

        #batch add
        $sql = FollowTagEntity::getDb()->createCommand()->batchInsert(
            FollowTagEntity::tableName(),
            ['user_id', 'follow_tag_id', 'created_at', 'updated_at'],
            $data
        )->getSql();

        $result = FollowTagEntity::getDb()->createCommand(
            sprintf(
                '%s ON DUPLICATE KEY UPDATE updated_at="%d", count_follow=count_follow+1',
                $sql,
                time()
            )
        )->execute();

        if ($result) {
            #user follow tag count
            Counter::userAddFollowTag($user_id, count($tag_ids));
            foreach ($tag_ids as $tag_id) {
                Counter::tagAddFollow($tag_id);
            }

            $params = [
                [RedisKey::REDIS_KEY_USER_TAG_RELATION, $user_id],
            ];

            foreach ($tag_ids as $tag_id) {
                $params[] = $created_at;
                $params[] = $tag_id;
            }
            call_user_func_array([Yii::$app->redis, 'zAdd'], $params);

        } else {
            Yii::error(sprintf('Batch Add Follow Tag %s', $result), __FUNCTION__);
        }

        return $result;
    }*/

    public static function removeFollowTag($follow_tag_ids, $user_id = null)
    {
        if (empty($user_id) || empty($follow_tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,follow_tag_ids']);
        }

        #delete
        /* @var $model FollowTagEntity */
        $model = FollowTagEntity::find()->where(
            [
                'follow_tag_id' => $follow_tag_ids,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        foreach ($model as $follow_tag) {
            $follow_tag->delete();
        }

        return true;
    }

    public static function getUserFollowTagIds($user_id, $page_no = 1, $page_size = 20)
    {
        $tag_ids = [];
        $cache_key = [RedisKey::REDIS_KEY_USER_TAG_RELATION, $user_id];

        if (0 == Yii::$app->redis->zCard($cache_key)) {
            $follow_tag_ids = FollowTagEntity::find()->select(['follow_tag_id', 'created_at'])->where(
                [
                    'user_id' => $user_id,
                ]
            )->orderBy('updated_at DESC')->limiter($page_no, $page_size)->asArray()->all();

            $params = [
                $cache_key,
            ];

            foreach ($follow_tag_ids as $tag) {
                $params[] = $tag['created_at'];
                $params[] = $tag['follow_tag_id'];
                $tag_ids[] = $tag['follow_tag_id'];
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
        $tag = TagService::getTagByTagId($follow_tag_id);

        if (!$tag) {
            throw new NotFoundModelException('tag', $follow_tag_id);
        }

        $cache_key = [RedisKey::REDIS_KEY_TAG_FOLLOW_USER_LIST, $follow_tag_id];

        if ($tag['count_follow'] < self::MAX_FOLLOW_TAG_COUNT_BY_USING_CACHE) {
            self::ensureUserOfFollowTagHasCached($cache_key, $follow_tag_id);

            $insert_cache_data = [];
            //存在则判断是否已存在集合中
            $cache_data = Yii::$app->redis->zScore($cache_key, $user_id);

            if ($cache_data === false) {
                $insert_cache_data[] = ['create_at' => TimeHelper::getCurrentTime(), 'user_id' => $user_id];
            }

            if ($insert_cache_data) {
                //添加到缓存中
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }

        return false;
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

        $tag = TagService::getTagByTagId($follow_tag_id);

        if (!$tag) {
            throw new NotFoundModelException('tag', $follow_tag_id);
        }

        $cache_key = [RedisKey::REDIS_KEY_TAG_FOLLOW_USER_LIST, $follow_tag_id];

        if ($tag['count_follow'] < self::MAX_FOLLOW_TAG_COUNT_BY_USING_CACHE) {
            self::ensureUserOfFollowTagHasCached($cache_key, $follow_tag_id);

            //小于1000，则使用缓存
            $result = Yii::$app->redis->zScore($cache_key, $user_id);
        } else {
            //大于则查询数据库
            $result = FollowTagEntity::find()->where(
                [
                    'user_id'       => $user_id,
                    'follow_tag_id' => $follow_tag_id,
                ]
            )->count(1);
        }

        return $result;
    }


    ###################### FOLLOW USER ######################
    public static function addFollowUser($follow_user_id, $user_id)
    {
        Yii::trace(sprintf('用户%d关注用户%d', $user_id, $follow_user_id), 'service');

        if (empty($user_id) || empty($follow_user_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,follow_user_id']);
        }

        //官方账号禁止关注
        if (UserService::checkWhetherIsOfficialAccount($user_id)) {
            return Error::set(Error::TYPE_FOLLOW_DO_NOT_ALLOW_TO_FOLLOW);
        }

        $user = UserService::getUserById($user_id);

        if ($user['count_follow_user'] > FollowUserEntity::MAX_FOLLOW_USER_NUMBER) {
            return Error::set(
                Error::TYPE_FOLLOW_USER_FOLLOW_TOO_MUCH_USER,
                FollowUserEntity::MAX_FOLLOW_USER_NUMBER
            );
        }

        if (!FollowUserEntity::findOne(
            [
                'follow_user_id' => $follow_user_id,
                'user_id'        => $user_id,
            ]
        )
        ) {
            $model = new FollowUserEntity();
            $model->user_id = $user_id;
            $model->follow_user_id = $follow_user_id;
            if ($model->save()) {
                $result = true;
            } else {
                throw new ModelSaveErrorException($model);
            }
        } else {
            $result = true;
        }

        Yii::trace(sprintf('关注结果 %s', var_export($result, true)), 'service');

        return $result;
    }

    public static function batchAddFollowUser(array $follow_user_ids, $user_id)
    {
        foreach ($follow_user_ids as $follow_user_id) {
            self::addFollowUser($follow_user_id, $user_id);
        }

        return true;
    }

    /**
     * @param array $follow_user_ids
     * @param null  $user_id when user_id is null, means delete user_id
     * @return bool
     * @throws \Exception
     */
    public static function removeFollowUser(array $follow_user_ids, $user_id = null)
    {
        if (empty($follow_user_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,follow_user_id']);
        }

        #delete
        /* @var $model FollowUserEntity */
        $model = FollowUserEntity::find()->where(
            [
                'follow_user_id' => $follow_user_ids,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        foreach ($model as $follow_user) {
            $follow_user->delete();
        }

        return true;
    }


    public static function addUserFansCache($follow_user_id, $user_id)
    {
        $user = UserService::getUserById($follow_user_id);

        $cache_key = [RedisKey::REDIS_KEY_USER_FANS_LIST, $follow_user_id];

        if ($user['count_follow'] < self::MAX_FOLLOW_USER_COUNT_BY_USING_CACHE) {
            self::ensureUserFansHasCached($cache_key, $follow_user_id);

            $insert_cache_data = [];
            //存在则判断是否已存在集合中
            $cache_data = Yii::$app->redis->zScore($cache_key, $user_id);

            if ($cache_data === false) {
                $insert_cache_data[] = ['create_at' => TimeHelper::getCurrentTime(), 'user_id' => $user_id];
            }

            if ($insert_cache_data) {
                //添加到缓存中
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }

        return false;
    }

    public static function removeUserFansCache($follow_user_id, $user_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_FANS_LIST, $follow_user_id];
        if (Yii::$app->redis->zScore($cache_key, $user_id) !== false) {
            //存在则移除
            return Yii::$app->redis->zRem($cache_key, $user_id);
        } else {
            return false;
        }
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
            $insert_cache_data = FollowUserEntity::find()->select(['created_at', 'user_id'])->where(
                [
                    'follow_user_id' => $user_id,
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
            $insert_cache_data = FollowUserEntity::find()->select(['created_at', 'follow_user_id'])->where(
                [
                    'user_id' => $user_id,
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
            $insert_cache_data = FollowQuestionEntity::find()->select(['created_at', 'user_id'])->where(
                [
                    'follow_question_id' => $follow_question_id,
                ]
            )->asArray()->all();

            if ($insert_cache_data) {
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }
    }

    public static function ensureUserOfFollowTagHasCached($cache_key, $follow_tag_id)
    {
        if (Yii::$app->redis->zCard($cache_key) == 0) {
            $insert_cache_data = FollowTagEntity::find()->select(['created_at', 'user_id'])->where(
                [
                    'follow_tag_id' => $follow_tag_id,
                ]
            )->asArray()->all();

            if ($insert_cache_data) {
                Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }
    }

    public static function checkUseIsFollowedUser($follow_user_id, $user_id)
    {
        if (Yii::$app->user->isGuest || $follow_user_id == $user_id) {
            return false;
        }

        /* @var $user CacheUserModel */
        $user = UserService::getUserById($follow_user_id);

        if (!$user) {
            throw new NotFoundModelException('user', $follow_user_id);
        }

        $cache_key = [RedisKey::REDIS_KEY_USER_FANS_LIST, $follow_user_id];

        if ($user->count_follow_user < self::MAX_FOLLOW_USER_COUNT_BY_USING_CACHE) {
            self::ensureUserFansHasCached($cache_key, $follow_user_id);

            //小于1000，则使用缓存
            $result = Yii::$app->redis->zScore($cache_key, $user_id);
        } else {
            //大于则查询数据库
            $result = FollowUserEntity::find()->where(
                [
                    'user_id'       => $user_id,
                    'follow_use_id' => $follow_user_id,
                ]
            )->count(1);
        }

        return $result;
    }

    ###################### FOLLOW TAG PASSIVE ######################

    /**
     * todo 使用arBEHAVIOR
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
        $created_at = time();

        foreach ($tag_ids as $tag_id) {
            $data[] = [$user_id, $tag_id, $created_at, $created_at];
        }

        #batch add
        $sql = FollowTagPassiveEntity::getDb()->createCommand()->batchInsert(
            FollowTagPassiveEntity::tableName(),
            ['user_id', 'follow_tag_id', 'created_at', 'updated_at'],
            $data
        )->getSql();

        $result = FollowTagPassiveEntity::getDb()->createCommand(
            sprintf(
                '%s ON DUPLICATE KEY UPDATE updated_at="%d", count_follow=count_follow+1',
                $sql,
                time()
            )
        )->execute();

        Yii::error(sprintf('Batch Add Passive Follow Tag %s', $result), __FUNCTION__);

        return $result;
    }

    /**
     * 根据标签ID获取推荐回答用户
     * @param array $tag_ids
     * @param int   $number
     * @return array ['tag_id'=>['user_id' => 'count_question', 'user_id' => 'count_question'], 'tag_id' => []]
     */
    public static function getRecommendUserIdsByTagIds(
        array $tag_ids,
        $number = FollowTagPassiveEntity::MAX_NUMBER_RECOMMEND_USER
    ) {
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
            $data = FollowTagPassiveEntity::find()->select(
                [
                    'follow_tag_id',
                    'user_id',
                    '`score`' => 'SUM(`count_follow`)',
                ]
            )->where(
                [
                    'follow_tag_id' => $tag_id,
                ]
            )->andWhere(
                'updated_at>=:updated_at',
                [':updated_at' => TimeHelper::getBeforeTime(FollowTagPassiveEntity::RECENT_PERIOD_OF_TIME)]
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
            $data = FollowTagPassiveEntity::find()->select(
                [
                    'follow_tag_id',
                    'count_follow',
                ]
            )->where(['user_id' => $user_id])->andWhere(
                'updated_at>=:updated_at',
                [':updated_at' => TimeHelper::getBeforeTime($period)]
            )->limit($limit)->orderBy(
                'count_follow DESC'
            )->asArray()->all();

            $rank = [];
            foreach ($data as $item) {
                $rank[$item['follow_tag_id']] = $item['count_follow'];
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
            $data = FollowTagPassiveEntity::find()->select(
                [
                    'user_id',
                    'count_follow',
                ]
            )->where(['follow_tag_id' => $tag_id])->andWhere(
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
            $user_ids = FollowUserEntity::find()->select(['user_id'])->where(
                [
                    'follow_user_id' => $user_id,
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
