<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_invite".
 *
 * @property string $id
 * @property string $question_id
 * @property integer $invited_user_id
 * @property string $created_by
 * @property string $created_at
 * @property string $status
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
            [['question_id'], 'required'],
            [['question_id', 'invited_user_id', 'created_by', 'created_at'], 'integer'],
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
            'created_by' => '邀请用户',
            'created_at' => '创建时间',
            'status' => '状态 progress处理中 completep完成　overtime超时未完成',
        ];
    }
}
