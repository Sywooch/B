<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_tag".
 *
 * @property string $question_id
 * @property string $tag_id
 * @property string $create_at
 * @property string $create_by
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
            [['question_id', 'tag_id', 'create_by'], 'required'],
            [['question_id', 'tag_id', 'create_at', 'create_by'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => 'Question ID',
            'tag_id' => 'Tag ID',
            'create_at' => '创建时间',
            'create_by' => '创建用户',
        ];
    }
}
