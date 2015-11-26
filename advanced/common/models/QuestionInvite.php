<?php

namespace common\models;

use common\components\Notifier;
use common\entities\NotificationEntity;
use common\services\QuestionService;
use Yii;

/**
 * This is the model class for table "question_invite".
 * @property string   $id
 * @property string   $question_id
 * @property integer  $invited_user_id
 * @property integer  $create_by
 * @property string   $create_at
 * @property string   $status
 * @property Question $question
 * @property User     $createBy
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
            [['status'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'question_id'     => '问题ID',
            'invited_user_id' => '被邀请的用户ID',
            'create_by'       => '邀请用户',
            'create_at'       => '创建时间',
            'status'          => '状态 progress处理中 completep完成　overtime超时未完成',
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


    public static function inviteToAnswerByNotice($invite_user_id, $be_invited_user_id, $question_id)
    {
        return Notifier::build()->from($invite_user_id)->to($be_invited_user_id)->notice(
            NotificationEntity::TYPE_INVITE_ME_TO_ANSWER_QUESTION,
            [
                'question_id' => $question_id,
            ]
        );
    }

    public static function inviteToAnswerByEmail($question_id, $email)
    {
        $question_data = QuestionService::getQuestionByQuestionId($question_id);

        if ($question_data) {
            #todo 需要模板支持
            return Notifier::build()->to($email)->email($question_data['subject'], '内容');
        }
    }
}
