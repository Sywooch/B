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
use common\helpers\TimeHelper;
use Yii;

class FollowService extends BaseService
{
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
            $model->created_at = $user_id;
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
     * 添加关注此问题的人
     * @param $question_id
     * @param $user_id
     */
    public static function addUserWhoFollowQuestion($question_id, $user_id)
    {
        //todo
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
        $model = FollowQuestionEntity::find()->where(
            [
                'follow_question_id' => $question_id,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        foreach ($model as $follow_question) {
            if ($follow_question->delete()) {
                Counter::cancelFollowQuestion($follow_question->user_id);
            }
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
    public static function addFollowTag($user_id, array $tag_ids)
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
            Counter::followTag($user_id, count($tag_ids));


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
    }

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
            if ($follow_tag->delete()) {
                Counter::cancelFollowTag($follow_tag->user_id);
            }
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


    ###################### FOLLOW USER ######################

    public static function addFollowUser(array $follow_user_ids, $user_id)
    {
        if (empty($user_id) || empty($follow_user_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, 'user_id,follow_user_ids');
        }

        if (UserService::checkWhetherIsOfficialAccount($user_id)) {
            return Error::set(Error::TYPE_FOLLOW_DO_NOT_ALLOW_TO_FOLLOW);
        }

        $follow_user_count = Yii::$app->user->profile->count_follow;

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
            #myself
            Counter::followUser($user_id, count($follow_user_ids));


            self::addUserFriendsToCache($user_id, $follow_user_ids);

            #be followed user
            foreach ($follow_user_ids as $follow_user_id) {
                Counter::beFollowUser($follow_user_id);
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
        $model = FollowUserEntity::find()->where(
            [
                'follow_user_id' => $follow_user_ids,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        #更新计数
        foreach ($model as $follow_user) {
            if ($follow_user->delete()) {
                if ($user_id) {
                    Counter::cancelFollowUser($user_id);
                }
                Counter::cancelBeFollowUser($follow_user->user_id);
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
