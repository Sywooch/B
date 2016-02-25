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
        Yii::$app->user->trigger(
            'event_question_create',
            new UserAssociationEvent(
                [
                    'id'             => $owner->id,
                    'type'           => AssociateModel::TYPE_QUESTION,
                    'associate_data' => [],
                    'notice_data'    => [],
                ]
            )
        );
    }

    public static function delete(QuestionEntity $owner)
    {
        Yii::$app->user->trigger(
            'event_question_delete',
            new UserAssociationEvent(
                [
                    'id'             => $owner->id,
                    'type'           => AssociateModel::TYPE_QUESTION,
                    'associate_data' => [],
                    'notice_data'    => [],
                ]
            )
        );
    }

    public static function update(QuestionEntity $owner)
    {
        if ($owner->created_by == Yii::$app->user->id) {
            //触发用户自己编辑问题
            Yii::$app->user->trigger(
                'event_question_update',
                new UserAssociationEvent(
                    [
                        'id'             => $owner->id,
                        'type'           => AssociateModel::TYPE_QUESTION,
                        'associate_data' => [],
                        'notice_data'    => [],
                    ]
                )
            );
        } else {
            //触发问题公共编辑，通知问题创建者
            $notice_data = new NoticeDataModel();
            $notice_data->sender = $owner->updated_by;
            $notice_data->receiver = $owner->created_by;

            Yii::$app->user->trigger(
                'event_question_common_edit',
                new UserAssociationEvent(
                    [
                        'id'             => $owner->id,
                        'type'           => AssociateModel::TYPE_QUESTION,
                        'associate_data' => [],
                        'notice_data'    => $notice_data,
                    ]
                )
            );
        }
    }

    public static function favorite(FavoriteEntity $owner)
    {
        $question = QuestionService::getQuestionByQuestionId($owner->associate_id);

        $notice_data = new NoticeDataModel();
        $notice_data->sender = $owner->created_by;
        $notice_data->receiver = $question->created_by;

        Yii::$app->user->trigger(
            'event_question_favorite',
            new UserAssociationEvent(
                [
                    'id'             => $owner->associate_id,
                    'type'           => AssociateModel::TYPE_QUESTION,
                    'associate_data' => [],
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function cancelFavorite(FavoriteEntity $owner)
    {
        //不通知用户
        $notice_data = [];

        Yii::$app->user->trigger(
            'event_question_cancel_favorite',
            new UserAssociationEvent(
                [
                    'id'             => $owner->associate_id,
                    'type'           => AssociateModel::TYPE_QUESTION,
                    'associate_data' => [],
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function follow(FollowEntity $owner)
    {
        $question = QuestionService::getQuestionByQuestionId($owner->associate_id);

        $notice_data = new NoticeDataModel();
        $notice_data->sender = $owner->user_id;
        $notice_data->receiver = $question->created_by;

        Yii::$app->user->trigger(
            'event_question_follow',
            new UserAssociationEvent(
                [
                    'id'             => $owner->associate_id,
                    'type'           => AssociateModel::TYPE_QUESTION,
                    'associate_data' => [],
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function cancelFollow(FollowEntity $owner)
    {
        $notice_data = new NoticeDataModel();

        Yii::$app->user->trigger(
            'event_question_cancel_follow',
            new UserAssociationEvent(
                [
                    'id'             => $owner->associate_id,
                    'type'           => AssociateModel::TYPE_QUESTION,
                    'associate_data' => [],
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function invite(QuestionInviteEntity $owner)
    {
        $notice_data = new NoticeDataModel();
        $notice_data->sender = $owner->created_by;
        $notice_data->receiver = $owner->invited_user_id;

        Yii::$app->user->trigger(
            'event_question_follow',
            new UserAssociationEvent(
                [
                    'id'             => $owner->question_id,
                    'type'           => AssociateModel::TYPE_QUESTION,
                    'associate_data' => [],
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function vote(VoteEntity $owner)
    {
        $question = QuestionService::getQuestionByQuestionId($owner->associate_id);

        //关联数据
        $associate_data = new AssociateDataModel();

        //通知数据
        $notice_data = new NoticeDataModel();
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
