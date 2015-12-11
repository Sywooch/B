<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/20
 * Time: 11:32
 */

namespace common\entities;

use common\models\Notification;
use Yii;

class NotificationEntity extends Notification
{
    const STATUS_UNREAD = 'unread';
    const STATUS_READ = 'read';

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'receiver']);
    }
}
