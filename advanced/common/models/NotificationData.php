<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification_data".
 *
 * @property string $notification_id
 * @property integer $sender
 * @property string $associate_data
 * @property string $created_at
 * @property string $status
 */
class NotificationData extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'sender'], 'required'],
            [['notification_id', 'sender', 'created_at'], 'integer'],
            [['status'], 'string'],
            [['associate_data'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'sender' => '发送方',
            'associate_data' => '关联数据，如回答某问题，则需要保存回答的ID',
            'created_at' => '创建时间',
            'status' => 'unread未读,  read已读',
        ];
    }
}
