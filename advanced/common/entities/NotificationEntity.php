<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/20
 * Time: 11:32
 */

namespace common\entities;

use common\behaviors\NotificationBehavior;
use common\behaviors\TimestampBehavior;
use common\models\Notification;
use Yii;
use yii\db\ActiveRecord;

class NotificationEntity extends Notification
{
    const STATUS_UNREAD = 'unread';
    const STATUS_READ = 'read';

    public function behaviors()
    {
        return [
            'timestamp'                => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            'notification_behavior' => [
                'class' => NotificationBehavior::className(),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'receiver']);
    }
}
