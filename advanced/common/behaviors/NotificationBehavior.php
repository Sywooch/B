<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 10:28
 */

namespace common\behaviors;

use Yii;
use yii\db\ActiveRecord;

class NotificationBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterNotificationInsert',
        ];
    }

    public function afterNotificationInsert()
    {
        $this->dealWithIncreaseCountNotification();
    }

    private function dealWithIncreaseCountNotification()
    {

    }
}
