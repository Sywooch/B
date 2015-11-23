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
use common\models\QuestionInvite;
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
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                ],
            ],
            'timestamp'                => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
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
                return false;
            }
        }
    }

    private function checkBeforeHasInvited($user_id, $invited_user_id, $question_id)
    {
        $model = self::find()->where(
            [
                'create_by'       => $user_id,
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
        $follow_user_ids = FollowUserEntity::getFollowUserIds($user_id);

        return $follow_user_ids;
    }

    /**
     * Data from follow_tag_passive
     * @param array $tag_id
     * @return mixed
     */
    public function getRecommendInviteUser(array $tag_id)
    {
        $result = FollowTagPassiveEntity::getRecommendUserIdsByTagIds($tag_id);

        return $result;
    }

    /*private function paddingRecommendUserData($data)
    {
        foreach ($data as $tag_id => $item) {
            foreach ($item as $user_id => $count_question) {

            }
        }
    }*/
}