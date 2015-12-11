<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_tag".
 *
 * @property string $question_id
 * @property string $tag_id
 * @property string $created_at
 * @property string $created_by
 */
class QuestionTag extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'tag_id'], 'required'],
            [['question_id', 'tag_id', 'created_at', 'created_by'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => '问题ID',
            'tag_id' => '标签ID',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
        ];
    }
}
