<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/16
 * Time: 17:05
 */

namespace common\behaviors;

use common\config\RedisKey;
use common\entities\UserEventEntity;
use common\models\CacheUserEventModel;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class UserEventBehavior
 * @package common\behaviors
 * @property \common\entities\UserEventEntity owner
 */
class UserEventBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'eventUserEventCreate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventUserEventUpdate',
            ActiveRecord::EVENT_AFTER_DELETE  => 'eventUserEventDelete',
        ];
    }

    public function eventUserEventCreate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $cache_key = [RedisKey::REDIS_KEY_USER_EVENT, $this->owner->event];
        $cache_data = (new CacheUserEventModel())->filter($this->owner->getAttributes());
        Yii::$app->redis->hMset($cache_key, $cache_data);
    }

    public function eventUserEventUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $cache_key = [RedisKey::REDIS_KEY_USER_EVENT, $this->owner->event];

        if ($this->owner->status == UserEventEntity::STATUS_ENABLE) {
            $cache_data = (new CacheUserEventModel())->filter($this->owner->getAttributes());
            Yii::$app->redis->hMset($cache_key, $cache_data);
        } else {
            Yii::$app->redis->hDel($cache_key);
        }
    }

    public function eventUserEventDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $cache_key = [RedisKey::REDIS_KEY_USER_EVENT, $this->owner->event];
        Yii::$app->redis->hDel($cache_key);
    }
}
