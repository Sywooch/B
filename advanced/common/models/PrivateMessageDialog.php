<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "private_message_dialog".
 *
 * @property string $id
 * @property string $private_message_id
 * @property string $content
 * @property string $created_at
 * @property integer $created_by
 * @property integer $read_at
 * @property string $ip
 * @property string $status
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
            [['private_message_id', 'created_by'], 'required'],
            [['private_message_id', 'created_at', 'created_by', 'read_at', 'ip'], 'integer'],
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
            'created_at' => '创建时间',
            'created_by' => 'Created By',
            'read_at' => '查看时间',
            'ip' => 'IP地址',
            'status' => '状态',
        ];
    }
}
