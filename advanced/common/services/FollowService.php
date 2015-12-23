<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 14:12
 */

namespace common\services;

use common\components\Counter;
use common\components\Error;
use common\entities\FollowQuestionEntity;
use common\entities\FollowTagEntity;
use common\entities\FollowTagPassiveEntity;
use common\entities\FollowUserEntity;
use common\exceptions\NotFoundModelException;
use common\helpers\TimeHelper;
use common\models\FollowTag;
use Yii;

class FollowService extends BaseService
{
    const MAX_FOLLOW_QUESTION_COUNT_BY_USING_CACHE = 1000;//当超过这个数时，关注此问题的人，不使用缓存
    const MAX_FOLLOW_TAG_COUNT_BY_USING_CACHE = 1000;

    ###################### FOLLOW QUESTION ######################
    /**
     * 添加关注
     * @param $question_id
     * @param $user_id
     * @return bool
     */
    public static function addFollowQuestion($question_id, $user_id)
    {
        Yii::trace(sprintf('用户%d关注问题%d', $user_id, $question_id), 'service');

        if (empty($user_id) || empty($question_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,question_id']);
        }

        $user = UserService::getUserById($user_id);

        if ($user['count_follow_question'] > FollowQuestionEntity::MAX_FOLLOW_NUMBER) {
            return Error::set(
                Error::TYPE_FOLLOW_QUESTION_FOLLOW_TOO_MUCH_QUESTION,
                FollowQuestionEntity::MAX_FOLLOW_NUMBER
            );
        }

        if (!FollowQuestionEntity::findOne(
            [
                'user_id'            => $user_id,
                'follow_question_id' => $question_id,
            ]
        )
        ) {
            $model = new FollowQuestionEntity;
            $model->user_id = $user_id;
            $model->follow_question_id = $question_id;
            if ($model->save()) {
                $result = true;
            } else {
                Yii::error($model->getErrors(), __FUNCTION__);

                $result = false;
            }
        } else {
            $result = true;
        }
        Yii::trace(sprintf('用户 %d 关注问题 %d ，结果 %s', $user_id, $question_id, var_export($result, true)), 'service');

        return $result;
    }

    /**
     * 添加关注此问题的人的缓存
     * 如果此问题关注人数已超过1000，则不使用缓存
     * @param $question_id
     * @param $user_id
     * @return mixed
     * @throws NotFoundModelException
     */
    public static function addUserOfFollowQuestionCache($question_id, $user_id)
    {
        $question = QuestionService::getQuestionByQuestionId($question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $question_id);
        }

        $insert_cache_data = [];
        $cache_key = [REDIS_KEY_QUESTION_FOLLOW_USER_LIST, $question_id];

        if ($question['count_follow'] < self::MAX_FOLLOW_QUESTION_COUNT_BY_USING_CACHE) {
            //小于1000，则使用缓存，大于，则不处理。
            if (Yii::$app->redis->sCard($cache_key) == 0) {
                //判断是否已存在集合缓存，不存在
                $insert_cache_data = FollowQuestionEntity::find()->select(['user_id'])->where(
                    [
                        'follow_question_id' => $question_id,
                    ]
                )->column();

            } else {
                //存在则判断是否已存在集合中
                $cache_data = Yii::$app->redis->sIsMember($cache_key, $user_id);
                if (!$cache_data) {
                    $insert_cache_data[] = $user_id;
                }
            }
        }

        if ($insert_cache_data) {
            //添加到缓存中
            $params = array_merge(
                [$cache_key],
                array_values($insert_cache_data)
            );

            return call_user_func_array([Yii::$app->redis, 'sAdd'], $params);
        } else {
            return false;
        }
    }

    /**
     * 移除关注此问题的用户
     * @param $question_id
     * @param $user_id
     * @return bool|int
     * @throws NotFoundModelException
     */
    public static function removeUserOfFollowQuestionCache($question_id, $user_id)
    {
        $question = QuestionService::getQuestionByQuestionId($question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $question_id);
        }

        $cache_key = [REDIS_KEY_QUESTION_FOLLOW_USER_LIST, $question_id];
        if (Yii::$app->redis->sIsMember($cache_key, $user_id)) {
            //存在则移除
            return Yii::$app->redis->sRem($cache_key, $user_id);
        } else {
            return true;
        }
    }

    /**
     * 判断用户是否已关注本问题
     * @param $question_id
     * @param $user_id
     * @return bool
     * @throws NotFoundModelException
     */
    public static function checkUseIsFollowedQuestion($question_id, $user_id)
    {
        $question = QuestionService::getQuestionByQuestionId($question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $question_id);
        }

        $cache_key = [REDIS_KEY_QUESTION_FOLLOW_USER_LIST, $question_id];

        if ($question['count_follow'] < self::MAX_FOLLOW_QUESTION_COUNT_BY_USING_CACHE) {
            if (Yii::$app->redis->sCard($cache_key) == 0) {
                //判断是否已存在集合缓存，不存在
                $insert_cache_data = FollowQuestionEntity::find()->select(['user_id'])->where(
                    [
                        'follow_question_id' => $question_id,
                    ]
                )->column();

                if ($insert_cache_data) {
                    //添加到缓存中
                    $params = array_merge(
                        [$cache_key],
                        array_values($insert_cache_data)
                    );

                    call_user_func_array([Yii::$app->redis, 'sAdd'], $params);
                }
            }

            //小于1000，则使用缓存
            $result = Yii::$app->redis->sIsMember($cache_key, $user_id);
        } else {
            //大于则查询数据库
            $result = FollowQuestionEntity::find()->where(
                [
                    'user_id'            => $user_id,
                    'follow_question_id' => $question_id,
                ]
            )->count(1);
        }

        return $result;
    }

    /**
     * @param      $question_id
     * @param null $user_id is null, delete all follow
     * @return bool
     * @throws \Exception
     */
    public static function removeFollowQuestion($question_id, $user_id = null)
    {
        if (empty($question_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,question_id']);
        }

        #delete
        /* @var $model FollowQuestionEntity */
        $model = FollowQuestionEntity::find()->where(
            [
                'follow_question_id' => $question_id,
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
     * @param     $question_id
     * @param int $limit
     * @return array|bool|\common\models\FollowQuestion[]
     */
    public static function getFollowQuestionUserIdsByQuestionId($question_id, $limit = 100)
    {
        $user_ids = FollowQuestionEntity::find()->select('user_id')->where(
            [
                'follow_question_id' => $question_id,
            ]
        )->limit($limit)->orderBy('created_at DESC')->column();

        return $user_ids;
    }
    
    public static function getUserFollowQuestionIdsByUserId($user_id, $page_no, $page_size)
    {
        $user_ids = FollowQuestionEntity::find()->select('follow_question_id')->where(
            [
                'user_id' => $user_id,
            ]
        )->limiter($page_no, $page_size)->asArray()->column();

        return $user_ids;
    }

    ###################### FOLLOW TAG ######################

    public static function addFollowTag($tag_id, $user_id)
    {
        Yii::trace(sprintf('用户%d关注标签%d', $user_id, $tag_id), 'service');

        if (empty($user_id) || empty($tag_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,tag_id']);
        }

        $user = UserService::getUserById($user_id);

        if ($user['count_follow_tag'] > FollowTagEntity::MAX_FOLLOW_NUMBER) {
            return Error::set(
                Error::TYPE_FOLLOW_TAG_FOLLOW_TOO_MUCH_TAG,
                FollowTagEntity::MAX_FOLLOW_NUMBER
            );
        }

        if (!FollowTagEntity::findOne(
            [
                'follow_tag_id' => $tag_id,
                'user_id'       => $user_id,
            ]
        )
        ) {
            $model = new FollowTagEntity();
            $model->user_id = $user_id;
            $model->follow_tag_id = $tag_id;
            if ($model->save()) {
                $result = true;
            } else {
                Yii::error($model->getErrors(), __FUNCTION__);

                $result = false;
            }
        } else {
            $result = true;
        }

        Yii::trace(sprintf('用户 %d 关注标签 %d ，结果 %s', $user_id, $tag_id, var_export($result, true)), 'service');

        return $result;
    }


    public static function batchAddFollowTag(array $tag_ids, $user_id)
    {
        foreach ($tag_ids as $tag_id) {
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
                [REDIS_KEY_USER_TAG_RELATION, $user_id],
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

    public static function removeFollowTag(array $tag_ids, $user_id = null)
    {
        if (empty($user_id) || empty($tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,tag_ids']);
        }

        #delete
        /* @var $model FollowTagEntity */
        $model = FollowTagEntity::find()->where(
            [
                'follow_tag_id' => $tag_ids,
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
        $cache_key = [REDIS_KEY_USER_TAG_RELATION, $user_id];

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

    /**
     * 添加此标签的用户缓存
     * @param $tag_id
     * @param $user_id
     * @return bool|mixed
     * @throws NotFoundModelException
     */
    public static function addUserOfFollowTagCache($tag_id, $user_id)
    {
        $tag = TagService::getTagByTagId($tag_id);

        if (!$tag) {
            throw new NotFoundModelException('tag', $tag_id);
        }

        $insert_cache_data = [];
        $cache_key = [REDIS_KEY_TAG_FOLLOW_USER_LIST, $tag_id];

        if ($tag['count_follow'] < self::MAX_FOLLOW_TAG_COUNT_BY_USING_CACHE) {
            //小于1000，则使用缓存，大于，则不处理。
            if (Yii::$app->redis->sCard($cache_key) == 0) {
                //判断是否已存在集合缓存，不存在
                $insert_cache_data = FollowTagEntity::find()->select(['user_id'])->where(
                    [
                        'follow_tag_id' => $tag_id,
                    ]
                )->column();

            } else {
                //存在则判断是否已存在集合中
                $cache_data = Yii::$app->redis->sIsMember($cache_key, $user_id);
                if (!$cache_data) {
                    $insert_cache_data[] = $user_id;
                }
            }
        }

        if ($insert_cache_data) {
            //添加到缓存中
            $params = array_merge(
                [$cache_key],
                array_values($insert_cache_data)
            );

            return call_user_func_array([Yii::$app->redis, 'sAdd'], $params);
        } else {
            return false;
        }
    }

    /**
     * 移除关注此标签的用户
     * @param $tag_id
     * @param $user_id
     * @return bool|int
     * @throws NotFoundModelException
     */
    public static function removeUserOfFollowTagCache($tag_id, $user_id)
    {
        $tag = TagService::getTagByTagId($tag_id);

        if (!$tag) {
            throw new NotFoundModelException('tag', $tag_id);
        }

        $cache_key = [REDIS_KEY_TAG_FOLLOW_USER_LIST, $tag_id];
        if (Yii::$app->redis->sIsMember($cache_key, $user_id)) {
            //存在则移除
            return Yii::$app->redis->sRem($cache_key, $user_id);
        } else {
            return true;
        }
    }

    public static function checkUseIsFollowedTag($tag_id, $user_id)
    {
        $tag = TagService::getTagByTagId($tag_id);

        if (!$tag) {
            throw new NotFoundModelException('tag', $tag_id);
        }

        $cache_key = [REDIS_KEY_TAG_FOLLOW_USER_LIST, $tag_id];

        if ($tag['count_follow'] < self::MAX_FOLLOW_TAG_COUNT_BY_USING_CACHE) {
            if (Yii::$app->redis->sCard($cache_key) == 0) {
                //判断是否已存在集合缓存，不存在
                $insert_cache_data = FollowTagEntity::find()->select(['user_id'])->where(
                    [
                        'follow_tag_id' => $tag_id,
                    ]
                )->column();

                if ($insert_cache_data) {
                    //添加到缓存中
                    $params = array_merge(
                        [$cache_key],
                        array_values($insert_cache_data)
                    );

                    call_user_func_array([Yii::$app->redis, 'sAdd'], $params);
                }
            }

            //小于1000，则使用缓存
            $result = Yii::$app->redis->sIsMember($cache_key, $user_id);
        } else {
            //大于则查询数据库
            $result = FollowTagEntity::find()->where(
                [
                    'user_id'       => $user_id,
                    'follow_tag_id' => $tag_id,
                ]
            )->count(1);
        }

        return $result;
    }


    ###################### FOLLOW USER ######################

    public static function addFollowUser(array $follow_user_ids, $user_id)
    {
        if (empty($user_id) || empty($follow_user_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, 'user_id,follow_user_ids');
        }

        if (UserService::checkWhetherIsOfficialAccount($user_id)) {
            return Error::set(Error::TYPE_FOLLOW_DO_NOT_ALLOW_TO_FOLLOW);
        }

        $follow_user_count = Yii::$app->user->profile->count_follow_user;

        //检查好友数量
        if ($follow_user_count + count($follow_user_ids) > FollowUserEntity::MAX_FOLLOW_USER_NUMBER) {
            return Error::set(
                Error::TYPE_FOLLOW_USER_FOLLOW_TOO_MUCH_USER,
                [
                    $follow_user_count,
                    FollowUserEntity::MAX_FOLLOW_USER_NUMBER,
                ]
            );
        }

        $data = [];
        $created_at = TimeHelper::getCurrentTime();
        foreach ($follow_user_ids as $follow_user_id) {
            $data[] = [$user_id, $follow_user_id, $created_at];
        }

        #batch add
        $sql = FollowUserEntity::getDb()->createCommand()->batchInsert(
            FollowUserEntity::tableName(),
            ['user_id', 'follow_user_id', 'created_at'],
            $data
        )->getSql();

        $result = FollowUserEntity::getDb()->createCommand(
            sprintf(
                '%s ON DUPLICATE KEY UPDATE created_at="%d"',
                $sql,
                time()
            )
        )->execute();

        if ($result) {
            //todo 需要放到behavior中
            #myself
            Counter::userAddFollowUser($user_id, count($follow_user_ids));

            self::addUserFriendsToCache($user_id, $follow_user_ids);

            #be followed user
            foreach ($follow_user_ids as $follow_user_id) {
                Counter::userAddFans($follow_user_id);
                self::addUserFansToCache($follow_user_id, [$user_id]);
            }

        } else {
            Yii::error(sprintf('Batch Add Follow Tag %s', $result), __FUNCTION__);
        }

        return $result;
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

        #更新计数
        foreach ($model as $follow_user) {
            if ($follow_user->delete()) {
                if ($user_id) {
                    Counter::userCancelFollowUser($user_id);
                }
                Counter::userCancelFans($follow_user->user_id);
            }
        }

        #更新好友、粉丝缓存
        if ($user_id) {
            self::removeUserFriendsToCache($user_id, $follow_user_ids);

            foreach ($follow_user_ids as $follow_user_id) {
                self::removeUserFansToCache($follow_user_id, [$user_id]);
            }
        }

        return true;
    }

    private static function addUserFriendsToCache($user_id, array $follow_user_id)
    {
        if ($follow_user_id) {
            $params = array_merge(
                [
                    [REDIS_KEY_USER_FRIENDS, $user_id],

                ],
                array_values($follow_user_id)
            );

            return call_user_func_array([Yii::$app->redis, 'sAdd'], $params);
        }

        return false;
    }

    private static function addUserFansToCache($user_id, array $follow_user_id)
    {
        if ($follow_user_id) {
            $params = array_merge(
                [
                    [REDIS_KEY_USER_FANS, $user_id],

                ],
                array_values($follow_user_id)
            );

            return call_user_func_array([Yii::$app->redis, 'sAdd'], $params);
        }

        return false;
    }

    private static function removeUserFriendsToCache($user_id, array $follow_user_id)
    {
        if ($follow_user_id) {
            $params = array_merge(
                [
                    [REDIS_KEY_USER_FRIENDS, $user_id],

                ],
                array_values($follow_user_id)
            );

            return call_user_func_array([Yii::$app->redis, 'sRem'], $params);
        }

        return false;
    }

    private static function removeUserFansToCache($user_id, array $follow_user_id)
    {
        if ($follow_user_id) {
            $params = array_merge(
                [
                    [REDIS_KEY_USER_FRIENDS, $user_id],

                ],
                array_values($follow_user_id)
            );

            return call_user_func_array([Yii::$app->redis, 'sRem'], $params);
        }

        return false;
    }

    ###################### FOLLOW TAG PASSIVE ######################

    public static function addFollowTagPassive($user_id, array $tag_ids)
    {
        if (empty($user_id) || empty($tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,tag_ids']);
        }

        $data = [];
        $created_at = time();

        foreach ($tag_ids as $tag_id) {
            $data[] = [$user_id, $tag_id, $created_at];
        }

        #batch add
        $sql = FollowTagPassiveEntity::getDb()->createCommand()->batchInsert(
            FollowTagPassiveEntity::tableName(),
            ['user_id', 'follow_tag_id', 'created_at'],
            $data
        )->getSql();

        $result = FollowTagPassiveEntity::getDb()->createCommand(
            sprintf(
                '%s ON DUPLICATE KEY UPDATE updated_at="%d", count_follow=count_follow+1',
                $sql,
                time()
            )
        )->execute();

        if ($result) {
            #user follow tag count

            /*$params = [
                [REDIS_KEY_USER_TAG_PASSIVE_RELATION, $user_id],
            ];

            foreach ($tag_ids as $tag_id) {
                $params[] = $created_at;
                $params[] = $tag_id;
            }
            call_user_func_array([Yii::$app->redis, 'zAdd'], $params);*/

        } else {
            Yii::error(sprintf('Batch Add Passive Follow Tag %s', $result), __FUNCTION__);
        }

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
        $cache_key = [REDIS_KEY_TAG_USER_RELATION, $tag_id];

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
    public static function getTagIdsWhichUserIsGoodAt($user_id, $limit = 20, $period = 30)
    {
        $cache_key = [REDIS_KEY_USER_IS_GOOD_AT_TAG_IDS, implode(':', [$user_id, $period])];
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
     * @param     $tag_id
     * @param int $limit
     * @param int $period
     * @return array|bool|string
     */
    public static function getUserWhichIsGoodAtThisTag($tag_id, $limit = 20, $period = 30)
    {
        $cache_key = [REDIS_KEY_TAG_WHICH_USER_IS_GOOD_AT, implode(':', [$tag_id, $period])];
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
     * @param     $user_id
     * @param int $page_no
     * @param int $page_size
     * @return array
     * @throws \yii\base\Exception
     */
    public static function getUserFanUserIds($user_id, $page_no = 1, $page_size = 20)
    {
        $cache_data = Yii::$app->redis->sMembers([REDIS_KEY_USER_FRIENDS, $user_id]);

        if (!$cache_data) {
            $query = FollowUserEntity::find()->select(['user_id'])->where(
                ['follow_user_id' => $user_id]
            )->orderBy('created_at DESC')->limiter($page_no, $page_size);

            $follow_user_id = $query->column();

            if (self::addUserFansToCache($user_id, $follow_user_id)) {
                $cache_data = $follow_user_id;
            }
        }

        return $cache_data;
    }

    /**
     * 获取用户好友
     * @param     $user_id
     * @param int $page_no
     * @param int $page_size
     * @return array
     * @throws \yii\base\Exception
     */
    public static function getUserFriendUserIds($user_id, $page_no = 1, $page_size = 20)
    {
        $cache_data = Yii::$app->redis->sMembers([REDIS_KEY_USER_FRIENDS, $user_id]);

        if (!$cache_data) {
            $follow_user_id = FollowUserEntity::find()->select(['follow_user_id'])->where(
                [
                    'user_id' => $user_id,
                ]
            )->orderBy('created_at DESC')->limiter($page_no, $page_size)->column();

            if (self::addUserFriendsToCache($user_id, $follow_user_id)) {
                $cache_data = $follow_user_id;
            }
        }

        return $cache_data;
    }
}
