<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property string $id
 * @property integer $sender
 * @property integer $receiver
 * @property string $notice_code
 * @property string $associative_data
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
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
            [['sender', 'receiver', 'notice_code', 'created_at', 'updated_at'], 'integer'],
            [['receiver', 'notice_code'], 'required'],
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
            'notice_code' => '通知类型代码',
            'associative_data' => '关联数据',
            'created_at' => '创建时间',
            'updated_at' => '查看时间',
            'status' => 'unread未读,  read已读',
        ];
    }
}
