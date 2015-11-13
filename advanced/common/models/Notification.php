<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property string $id
 * @property integer $sender
 * @property integer $receiver
 * @property integer $type_code
 * @property string $associative_data
 * @property string $status
 * @property string $create_at
 * @property string $read_at
 *
 * @property User $receiver0
 */
class Notification extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender', 'receiver', 'type_code', 'create_at', 'read_at'], 'integer'],
            [['receiver', 'type_code'], 'required'],
            [['status'], 'string'],
            [['associative_data'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender' => '发送方，为空则为系统通知',
            'receiver' => '接收通知的用户ID',
            'type_code' => '通知类型代码',
            'associative_data' => '关联数据',
            'status' => 'unread未读,  read已读',
            'create_at' => '创建时间',
            'read_at' => '查看时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver0()
    {
        return $this->hasOne(User::className(), ['id' => 'receiver']);
    }
}
