<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "private_message".
 *
 * @property string $id
 * @property integer $sender
 * @property integer $receiver
 * @property integer $create_at
 * @property string $sender_remove
 * @property string $receiver_remove
 * @property string $last_message
 * @property integer $active_at
 * @property integer $active_by
 *
 * @property User $sender0
 * @property User $receiver0
 * @property PrivateMessageDialog[] $privateMessageDialogs
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
            [['sender', 'receiver', 'create_at', 'active_at', 'active_by'], 'integer'],
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
            'create_at' => '创建时间',
            'sender_remove' => '发送方删除',
            'receiver_remove' => '接收方删除',
            'last_message' => 'Last Message',
            'active_at' => '最后活动时间',
            'active_by' => '最后活动的用户ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender0()
    {
        return $this->hasOne(User::className(), ['id' => 'sender']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver0()
    {
        return $this->hasOne(User::className(), ['id' => 'receiver']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrivateMessageDialogs()
    {
        return $this->hasMany(PrivateMessageDialog::className(), ['private_message_id' => 'id']);
    }
}
