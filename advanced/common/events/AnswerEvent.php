<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-02-25
 * Time: 11:29
 */

namespace common\events;

use common\behaviors\AnswerBehavior;
use common\components\user\UserAssociationEvent;
use common\entities\AnswerEntity;
use common\entities\VoteEntity;
use common\helpers\StringHelper;
use common\models\AssociateDataModel;
use common\models\AssociateModel;
use common\models\CacheQuestionModel;
use common\models\NoticeDataModel;
use common\services\AnswerService;
use common\services\FollowService;
use Yii;

class AnswerEvent extends BaseUserEvent
{
    public static function create(AnswerEntity $owner)
    {
        //关联数据
        $associate_data = new AssociateDataModel();
        $associate_data->answer_id = $owner->id;
        //通知数据
        $notice_data = new NoticeDataModel();
        $notice_data->sender = $owner->created_by;
        $notice_data->receiver = self::getReceiverUserIds($owner);


        Yii::$app->user->trigger(
            'event_answer_create',
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->question_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function delete(AnswerEntity $owner)
    {
        //关联数据
        $associate_data = new AssociateDataModel();
        $associate_data->answer_id = $owner->id;
        //通知数据
        $notice_data = new NoticeDataModel();
        $notice_data->sender = Yii::$app->user->id;
        $notice_data->receiver = self::getReceiverUserIds($owner);

        Yii::$app->user->trigger(
            'event_answer_delete',
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->question_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function update(AnswerEntity $owner)
    {
        //关联数据
        $associate_data = new AssociateDataModel();
        $associate_data->answer_id = $owner->id;

        //通知数据
        $notice_data = new NoticeDataModel();

        if (Yii::$app->user->id != $owner->created_by) {
            //公共编辑

            $notice_data->sender = Yii::$app->user->id;
            $notice_data->receiver = $owner->created_by;

            Yii::$app->user->trigger(
                'event_answer_common_edit',
                new UserAssociationEvent(
                    [
                        'associate_id'   => $owner->question_id,
                        'associate_type' => AssociateModel::TYPE_QUESTION,
                        'associate_data' => $associate_data,
                        'notice_data'    => $notice_data,
                    ]
                )
            );
        } else {
            Yii::$app->user->trigger(
                'event_answer_update',
                new UserAssociationEvent(
                    [
                        'associate_id'   => $owner->question_id,
                        'associate_type' => AssociateModel::TYPE_QUESTION,
                        'associate_data' => $associate_data,
                        'notice_data'    => $notice_data,
                    ]
                )
            );
        }

    }

    public static function vote(VoteEntity $owner)
    {
        $answer = AnswerService::getAnswerByAnswerId($owner->associate_id);

        //关联数据
        $associate_data = new AssociateDataModel();
        $associate_data->answer_id = $owner->associate_id;

        //通知数据
        $notice_data = new NoticeDataModel();
        $notice_data->sender = $owner->created_by;
        $notice_data->receiver = $answer->created_by;

        Yii::$app->user->trigger(
            'event_answer_vote',
            new UserAssociationEvent(
                [
                    'associate_id'   => $answer->question_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    private static function getReceiverUserIds(AnswerEntity $owner)
    {
        #当前回复内容超过指定长度，通知所有关注者，否则只通知提问者
        if (StringHelper::countStringLength($owner->content) >= AnswerBehavior::NEED_NOTIFICATION_ANSWER_CONTENT_LENGTH) {
            $user_ids = FollowService::getFollowQuestionUserIdsByQuestionId($owner->question_id);
        } else {
            /* @var $question CacheQuestionModel */
            $question = $owner->getQuestion();
            $user_ids = [$question->created_by];
        }

        return $user_ids;
    }
}
