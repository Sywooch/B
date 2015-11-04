<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_invite".
 *
 * @property string $id
 * @property string $question_id
 * @property integer $invited_user_id
 * @property integer $create_by
 * @property string $create_at
 * @property string $status
 *
 * @property Question $question
 * @property User $createBy
 */
class QuestionInvite extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_invite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'create_by'], 'required'],
            [['question_id', 'invited_user_id', 'create_by', 'create_at'], 'integer'],
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
            'invited_user_id' => '被邀请的用户ID',
            'create_by' => '邀请用户',
            'create_at' => '创建时间',
            'status' => '状态 progress处理中 completep完成　overtime超时未完成',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBy()
    {
        return $this->hasOne(User::className(), ['id' => 'create_by']);
    }
}
