<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 10:28
 */

namespace common\behaviors;

use common\entities\NotificationEntity;
use common\services\UserService;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class NotificationBehavior
 * @package common\behaviors
 * @property NotificationEntity owner
 */
class NotificationBehavior extends BaseBehavior
{
    public $dirty_status;

    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT   => 'afterNotificationInsert',
            ActiveRecord::EVENT_AFTER_UPDATE   => 'afterNotificationUpdate',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterNotificationValidate',
        ];
    }

    public function afterNotificationValidate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $this->dirty_status = $this->owner->getDirtyAttributes(['status']);
    }

    public function afterNotificationInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithIncreaseCountNotification();
    }

    public function afterNotificationUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //已读重新转未读时，通知数+1
        if ($this->dirty_status) {
            $this->dealWithIncreaseCountNotification();
        }
    }

    /**
     * 通知数+1
     */
    private function dealWithIncreaseCountNotification()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        UserService::increaseNotificationCount($this->owner->receiver);
    }
}
