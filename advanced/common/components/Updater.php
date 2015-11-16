<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/22
 * Time: 9:45
 */

namespace common\components;


use common\entities\UserEntity;
use common\entities\UserProfileEntity;
use Yii;

class Updater extends BaseUpdater
{
    public static function clearNotifyCount($user_id)
    {
        $result = self::build()->table(UserProfileEntity::tableName())->set(
            [
                'count_notification' => 0,
            ]
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hSet([REDIS_KEY_USER, $user_id], 'count_notification', 0);
        }

        return $result;
    }
}