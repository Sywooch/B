<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 10:28
 */

namespace common\behaviors;


use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class NotificationBehavior extends Behavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterNotificationInsert',
        ];
    }

    public function afterNotificationInsert($event)
    {
        $this->dealWithIncreaseCountNotification();
    }

    private function dealWithIncreaseCountNotification()
    {

    }
}