<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property string $id
 * @property string $from_user_id
 * @property integer $to_user_id
 * @property string $type
 * @property string $source_id
 * @property string $status
 * @property string $create_at
 * @property string $read_at
 *
 * @property User $toUser
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
            [['from_user_id', 'to_user_id', 'create_at', 'read_at'], 'integer'],
            [['to_user_id', 'type'], 'required'],
            [['status'], 'string'],
            [['type', 'source_id'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user_id' => '发送通知的用户ID，NULL为系统',
            'to_user_id' => '接收通知的用户ID',
            'type' => '通知类型',
            'source_id' => '通知中关联的对象ID，可以是question_id,answer_id,comment_id等中的一个，视通知类型而定',
            'status' => 'unread未读,  read已读',
            'create_at' => '创建时间',
            'read_at' => '查看时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::className(), ['id' => 'to_user_id']);
    }
}
