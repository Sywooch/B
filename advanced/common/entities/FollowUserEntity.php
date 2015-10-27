<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/27
 * Time: 10:09
 */

namespace common\entities;


use common\models\FollowUser;
use Yii;
use yii\base\ErrorException;

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

    public function addFollow($user_id, array $follow_user_ids)
    {
        if (empty($user_id) || empty($follow_user_ids)) {
            throw new ParamsInvalidException(['user_id', 'follow_user_ids']);
        }

        $follow_user_count = Yii::$app->user->profile->count_follow;

        if ($follow_user_count + count($follow_user_ids) > self::MAX_FOLLOW_NUMBER) {
            throw new ErrorException(sprintf('你当前的关注用户的数量已超过限制，最多%d个用户，请先清理一下。', self::MAX_FOLLOW_NUMBER));
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
            Counter::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
                'count_follow_user',
                count($follow_user_ids)
            )->execute();
        } else {
            Yii::error(sprintf('Batch Add Follow Tag %s', $result), __FUNCTION__);
        }

        return $result;
    }

    public function removeFollow($follow_user_id, $user_id = null)
    {
        if (empty($follow_user_id)) {
            throw new ParamsInvalidException(['user_id', 'tag_id']);
        }

        #delete
        $model = self::find(
            [
                'follow_user_id' => $follow_user_id,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        foreach ($model as $follow_user) {
            if ($follow_user->delete()) {
                Counter::build()->set(UserProfileEntity::tableName(), $follow_user->user_id, 'user_id')->value(
                    'count_follow_user',
                    -1
                )->execute();
            }
        }

        return true;
    }
}