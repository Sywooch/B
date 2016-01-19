<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2016/1/16
 * Time: 20:40
 */

namespace common\services;

use common\config\RedisKey;
use common\entities\UserEventEntity;
use common\models\CacheUserEventModel;
use Yii;

class UserEventService extends BaseService
{
    /**
     * @param $event_name
     * @return CacheUserEventModel
     */
    public static function getUserEventByEventName($event_name)
    {
        $cache_key = [RedisKey::REDIS_KEY_USER_EVENT, $event_name];
        $cache_data = Yii::$app->redis->hGetAll($cache_key);

        $cacheUserEventModel = new CacheUserEventModel();

        if (empty($cache_data)) {
            $data = UserEventEntity::find()->where(
                [
                    'event'  => $event_name,
                    'status' => UserEventEntity::STATUS_ENABLE,
                ]
            )->one();

            if ($data) {
                $cache_data = $cacheUserEventModel->filter($data);
                Yii::$app->redis->hMset($cache_key, $cache_data);
            } else {
                $cache_data = false;
            }
        }

        return $cacheUserEventModel->build($cache_data);
    }
}
