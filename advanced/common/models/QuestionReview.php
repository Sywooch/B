<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_review".
 *
 * @property string $id
 * @property string $question_id
 * @property integer $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $updated_by
 * @property string $status
 */
class QuestionReview extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_review';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'created_by'], 'required'],
            [['question_id', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => '问题ID',
            'created_by' => '第一个修改用户',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'updated_by' => '最后修改用户',
            'status' => '状态 progress处理中 completep完成　overtime超时未完成',
        ];
    }
}
