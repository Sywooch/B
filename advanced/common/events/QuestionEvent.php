<?php
/**
 * 问题相关事件
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-02-25
 * Time: 11:28
 */

namespace common\events;

use common\components\user\UserAssociationEvent;
use common\entities\FavoriteEntity;
use common\entities\FollowEntity;
use common\entities\QuestionEntity;
use common\entities\QuestionInviteEntity;
use common\entities\VoteEntity;
use common\models\AssociateDataModel;
use common\models\AssociateModel;
use common\models\NoticeDataModel;
use common\services\QuestionService;
use Yii;

class QuestionEvent extends BaseUserEvent
{
    public static function create(QuestionEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        Yii::$app->user->trigger(
            'event_question_create',
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function delete(QuestionEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        Yii::$app->user->trigger(
            'event_question_delete',
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function update(QuestionEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        if ($owner->created_by == Yii::$app->user->id) {
            //触发用户自己编辑问题
            Yii::$app->user->trigger(
                'event_question_update',
                new UserAssociationEvent(
                    [
                        'associate_id'   => $owner->id,
                        'associate_type' => AssociateModel::TYPE_QUESTION,
                        'associate_data' => $associate_data,
                        'notice_data'    => $notice_data,
                    ]
                )
            );
        } else {
            //触发问题公共编辑，通知问题创建者
            $notice_data->sender = $owner->updated_by;
            $notice_data->receiver = $owner->created_by;

            Yii::$app->user->trigger(
                'event_question_common_edit',
                new UserAssociationEvent(
                    [
                        'associate_id'   => $owner->id,
                        'associate_type' => AssociateModel::TYPE_QUESTION,
                        'associate_data' => $associate_data,
                        'notice_data'    => $notice_data,
                    ]
                )
            );
        }
    }

    public static function favorite(FavoriteEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        $question = QuestionService::getQuestionByQuestionId($owner->associate_id);
        $notice_data->sender = $owner->created_by;
        $notice_data->receiver = $question->created_by;

        Yii::$app->user->trigger(
            'event_question_favorite',
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->associate_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function cancelFavorite(FavoriteEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        Yii::$app->user->trigger(
            'event_question_cancel_favorite',
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->associate_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function follow(FollowEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        $question = QuestionService::getQuestionByQuestionId($owner->associate_id);
        $notice_data->sender = $owner->user_id;
        $notice_data->receiver = $question->created_by;

        Yii::$app->user->trigger(
            'event_question_follow',
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->associate_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function cancelFollow(FollowEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        Yii::$app->user->trigger(
            'event_question_cancel_follow',
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->associate_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function invite(QuestionInviteEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        $notice_data->sender = $owner->created_by;
        $notice_data->receiver = $owner->invited_user_id;

        Yii::$app->user->trigger(
            'event_question_follow',
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

    public static function vote(VoteEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        $question = QuestionService::getQuestionByQuestionId($owner->associate_id);
        //通知数据
        $notice_data->sender = $owner->created_by;
        $notice_data->receiver = $question->created_by;

        Yii::$app->user->trigger(
            'event_question_vote',
            new UserAssociationEvent(
                [
                    'associate_id'   => $question->id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }
}
