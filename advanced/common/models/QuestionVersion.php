<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_version".
 * @property string $id
 * @property string $question_id
 * @property string  $subject
 * @property string  $tags
 * @property string  $content
 * @property integer $change_type
 * @property string $reason
 * @property string $ip
 * @property string $created_at
 * @property integer $created_by
 */
class QuestionVersion extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'created_by'], 'required'],
            [['question_id', 'change_type', 'ip', 'created_at', 'created_by'], 'integer'],
            [['content'], 'string'],
            [['subject', 'reason'], 'string', 'max' => 45],
            [['tags'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => '关联的问题ID',
            'subject'     => '标题',
            'tags'        => '标签',
            'content'     => '内容',
            'change_type' => '改变类型',
            'reason' => '为什么进行该操作',
            'ip' => 'IP地址',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
        ];
    }
}
