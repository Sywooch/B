<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_review".
 *
 * @property string $id
 * @property string $question_id
 * @property integer $create_by
 * @property string $create_at
 * @property string $modify_at
 * @property integer $modify_by
 * @property string $status
 *
 * @property User $createBy
 * @property Question $question
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
            [['question_id', 'create_by'], 'required'],
            [['question_id', 'create_by', 'create_at', 'modify_at', 'modify_by'], 'integer'],
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
            'create_by' => '第一个修改用户',
            'create_at' => '创建时间',
            'modify_at' => '修改时间',
            'modify_by' => '最后修改用户',
            'status' => '状态 progress处理中 completep完成　overtime超时未完成',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBy()
    {
        return $this->hasOne(User::className(), ['id' => 'create_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }
}
