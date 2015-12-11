<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "private_message".
 *
 * @property string $id
 * @property integer $sender
 * @property integer $receiver
 * @property string $sender_remove
 * @property string $receiver_remove
 * @property string $sender_read_at
 * @property string $receiver_read_at
 * @property string $last_message
 * @property integer $updated_at
 * @property integer $updated_by
 * @property string $count_message
 * @property integer $created_at
 */
class PrivateMessage extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'private_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender', 'receiver'], 'required'],
            [['sender', 'receiver', 'sender_read_at', 'receiver_read_at', 'updated_at', 'updated_by', 'count_message', 'created_at'], 'integer'],
            [['sender_remove', 'receiver_remove'], 'string'],
            [['last_message'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender' => '发送方',
            'receiver' => '接收方',
            'sender_remove' => '发送方删除',
            'receiver_remove' => '接收方删除',
            'sender_read_at' => '发送方阅读时间',
            'receiver_read_at' => '接收方阅读时间',
            'last_message' => 'Last Message',
            'updated_at' => '最后活动时间',
            'updated_by' => '最后活动的用户ID',
            'count_message' => '消息数',
            'created_at' => '创建时间',
        ];
    }
}
