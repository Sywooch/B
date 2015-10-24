<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "private_message_dialog".
 *
 * @property string $id
 * @property string $private_message_id
 * @property string $content
 * @property integer $create_by
 * @property string $create_at
 * @property integer $read_at
 * @property string $ip
 * @property string $status
 *
 * @property PrivateMessage $privateMessage
 * @property User $createBy
 */
class PrivateMessageDialog extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'private_message_dialog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['private_message_id', 'create_by'], 'required'],
            [['private_message_id', 'create_by', 'create_at', 'read_at', 'ip'], 'integer'],
            [['status'], 'string'],
            [['content'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'private_message_id' => '对话ID',
            'content' => '对话内容',
            'create_by' => 'Create By',
            'create_at' => '创建时间',
            'read_at' => '查看时间',
            'ip' => 'IP地址',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrivateMessage()
    {
        return $this->hasOne(PrivateMessage::className(), ['id' => 'private_message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBy()
    {
        return $this->hasOne(User::className(), ['id' => 'create_by']);
    }
}
