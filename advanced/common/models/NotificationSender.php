<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification_sender".
 *
 * @property string $notification_id
 * @property integer $sender
 * @property string $created_at
 * @property string $status
 */
class NotificationSender extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_sender';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'sender'], 'required'],
            [['notification_id', 'sender', 'created_at'], 'integer'],
            [['status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'sender' => '发送方，为空则为系统通知',
            'created_at' => '创建时间',
            'status' => 'unread未读,  read已读',
        ];
    }
}
