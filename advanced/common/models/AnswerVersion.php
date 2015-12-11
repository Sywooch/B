<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "answer_version".
 *
 * @property string $id
 * @property string $answer_id
 * @property string $content
 * @property string $reason
 * @property string $created_by
 * @property string $created_at
 */
class AnswerVersion extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['answer_id', 'content'], 'required'],
            [['answer_id', 'created_by', 'created_at'], 'integer'],
            [['content'], 'string'],
            [['reason'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'answer_id' => 'Answer ID',
            'content' => 'Content',
            'reason' => 'Reason',
            'created_by' => '创建用户',
            'created_at' => '创建时间',
        ];
    }
}
