<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/27
 * Time: 10:09
 */

namespace common\entities;


use common\behaviors\TimestampBehavior;
use common\components\Counter;
use common\components\Error;
use common\models\FollowUser;
use Yii;
use yii\base\ErrorException;
use yii\db\ActiveRecord;

class FollowUserEntity extends FollowUser
{
    const MAX_FOLLOW_NUMBER = 1000;


    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],
        ];
    }

    public function addFollow(array $follow_user_ids, $user_id)
    {
        if (empty($user_id) || empty($follow_user_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, 'user_id,follow_user_ids');
        }

        if (UserEntity::checkWhetherIsOfficialAccount($user_id)) {
            return Error::set(Error::TYPE_FOLLOW_DO_NOT_ALLOW_TO_FOLLOW);
        }

        $follow_user_count = Yii::$app->user->profile->count_follow;

        if ($follow_user_count + count($follow_user_ids) > self::MAX_FOLLOW_NUMBER) {
            return Error::set(
                Error::TYPE_FOLLOW_USER_FOLLOW_TOO_MUCH_USER,
                [
                    $follow_user_count,
                    self::MAX_FOLLOW_NUMBER,
                ]
            );
        }

        $create_at = time();
        foreach ($follow_user_ids as $follow_user_id) {
            $data[] = [$user_id, $follow_user_id, $create_at];
        }

        #batch add
        $sql = self::getDb()->createCommand()->batchInsert(
            self::tableName(),
            ['user_id', 'follow_user_id', 'create_at'],
            $data
        )->getSql();

        $result = self::getDb()->createCommand(
            sprintf(
                '%s ON DUPLICATE KEY UPDATE create_at="%d"',
                $sql,
                time()
            )
        )->execute();

        if ($result) {
            # increase myself & follow user
            Counter::followUser($user_id, count($follow_user_ids));

            foreach ($follow_user_ids as $follow_user_id) {
                Counter::beFollowUser($follow_user_id);
            }

            #add to cache
            $this->addFollowUserToCache($user_id, $follow_user_ids);

        } else {
            Yii::error(sprintf('Batch Add Follow Tag %s', $result), __FUNCTION__);
        }

        return $result;
    }

    /**
     * @param array $follow_user_id
     * @param null  $user_id when user_id is null, means delete user_id
     * @return bool
     * @throws ParamsInvalidException
     * @throws \Exception
     */
    public function removeFollow(array $follow_user_id, $user_id = null)
    {
        if (empty($follow_user_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,follow_user_id']);
        }

        #delete
        $model = self::find(
            [
                'follow_user_id' => $follow_user_id,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        #decrease myself & follow user
        foreach ($model as $follow_user) {
            if ($follow_user->delete()) {
                if ($user_id) {
                    Counter::cancelFollowUser($user_id);
                }
                Counter::cancelBeFollowUser($follow_user->user_id);
            }
        }

        #udpate follow use cache
        if ($user_id) {
            $this->removeFollowUserToCache($user_id, $follow_user_id);
        }

        return true;
    }


    public function getFollowUserIds($user_id, $limit = 1000)
    {
        return $this->getFollowUserIdsUseCache($user_id, $limit);
    }

    private function getFollowUserIdsUseCache($user_id, $limit)
    {
        $cache_data = Yii::$app->redis->sMembers([REDIS_KEY_USER_FOLLOW, $user_id]);

        if (!$cache_data) {
            $model = self::find()->select(['follow_user_id'])->where(
                [
                    'user_id' => $user_id,
                ]
            )->orderBy('create_at DESC')->limit($limit)->column();

            if ($this->addFollowUserToCache($user_id, $model)) {
                $cache_data = $model;
            }
        }

        return $cache_data;
    }

    private function addFollowUserToCache($user_id, array $follow_user_id)
    {
        if ($follow_user_id) {
            $params = array_merge(
                [
                    [REDIS_KEY_USER_FOLLOW, $user_id],

                ],
                array_values($follow_user_id)
            );

            return call_user_func_array([Yii::$app->redis, 'sAdd'], $params);
        }
    }

    private function removeFollowUserToCache($user_id, array $follow_user_id)
    {
        if ($follow_user_id) {
            $params = array_merge(
                [
                    [REDIS_KEY_USER_FOLLOW, $user_id],

                ],
                array_values($follow_user_id)
            );

            return call_user_func_array([Yii::$app->redis, 'sRem'], $params);
        }
    }
}