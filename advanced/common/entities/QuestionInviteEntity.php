<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/28
 * Time: 10:06
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\QuestionInviteBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\components\Notifier;
use common\exceptions\ModelSaveErrorException;
use common\models\QuestionInvite;
use common\services\FollowService;
use common\services\NotificationService;
use yii\db\ActiveRecord;
use Yii;

class QuestionInviteEntity extends QuestionInvite
{
    const STATUS_PROGRESS = 'progress';
    const STATUS_COMPLETE = 'complete';
    const STATUS_OVERTIME = 'overtime';

    public function behaviors()
    {
        return [
            'operator'                 => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                ],
            ],
            'timestamp'                => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
            ],
            'question_invite_behavior' => [
                'class' => QuestionInviteBehavior::className(),
            ],
        ];
    }

    public function inviteAnswer($user_id, $invited_user_id, $question_id)
    {
        if (!$this->checkBeforeHasInvited($user_id, $invited_user_id, $question_id)) {
            $model = clone $this;
            $model->invited_user_id = $invited_user_id;
            $model->question_id = $question_id;
            $model->status = self::STATUS_PROGRESS;
            if ($model->save()) {
                return true;
            } else {
                throw new ModelSaveErrorException($model);
            }
        } else {
            return Error::set(Error::TYPE_ANSWER_HAS_BEEN_INVITED);
        }
    }

    private function checkBeforeHasInvited($user_id, $invited_user_id, $question_id)
    {
        /* @var $model QuestionInviteEntity*/
        $model = self::find()->where(
            [
                'created_by'       => $user_id,
                'invited_user_id' => $invited_user_id,
                'question_id'     => $question_id,
            ]
        )->one();

        if ($model) {
            if ($model->status == self::STATUS_OVERTIME) {
                $model->status = self::STATUS_PROGRESS;
                $model->save();
            }

            return true;
        } else {
            return false;
        }
    }

    public function getInviteUserIds($user_id)
    {
        $follow_user_ids = FollowService::getFollowUserIds($user_id);

        return $follow_user_ids;
    }

    /**
     * Data from follow_tag_passive
     * @param array $tag_id
     * @return mixed
     */
    public function getRecommendInviteUser(array $tag_id)
    {
        $result = FollowService::getRecommendUserIdsByTagIds($tag_id);

        return $result;
    }

    /*private function paddingRecommendUserData($data)
    {
        foreach ($data as $tag_id => $item) {
            foreach ($item as $user_id => $count_question) {

            }
        }
    }*/


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(QuestionEntity::className(), ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'created_by']);
    }


    public static function inviteToAnswerByNotice($invite_user_id, $be_invited_user_id, $question_id)
    {
        return Notifier::build()->from($invite_user_id)->to($be_invited_user_id)->notice(
            NotificationService::TYPE_INVITE_ME_TO_ANSWER_QUESTION,
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
