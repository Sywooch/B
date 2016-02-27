<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-02-25
 * Time: 11:29
 */

namespace common\events;

use common\components\user\UserAssociationEvent;
use common\entities\CommentEntity;
use common\entities\VoteEntity;
use common\helpers\AtHelper;
use common\models\AssociateDataModel;
use common\models\AssociateModel;
use common\models\NoticeDataModel;
use common\services\AnswerService;

use common\services\CommentService;
use common\services\UserService;
use Yii;

class CommentEvent extends BaseUserEvent
{
    public static function create(CommentEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        //关联数据
        $associate_data->answer_id = $owner->associate_id;
        $associate_data->comment_id = $owner->id;

        //通知数据
        if ($owner->associate_type == AssociateModel::TYPE_ANSWER_COMMENT) {
            $answer_data = AnswerService::getAnswerByAnswerId($owner->associate_id);
            $notice_data->sender = $owner->created_by;
            $notice_data->receiver = $answer_data->created_by;
        }

        //触发用户事件
        Yii::$app->user->trigger(
            sprintf('event_%s_create', $owner->associate_type),
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->getAnswer()->question_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function delete(CommentEntity $owner)
    {
        //关联数据
        $associate_data = new AssociateDataModel();
        $associate_data->answer_id = $owner->associate_id;
        $associate_data->comment_id = $owner->id;

        //通知数据，不是本人删除，通知评论创建者
        $notice_data = new NoticeDataModel();
        if (Yii::$app->user->id != $owner->created_by) {
            $notice_data->sender = Yii::$app->user->id;
            $notice_data->receiver = $owner->created_by;
        }

        //触发用户事件
        Yii::$app->user->trigger(
            sprintf('event_%s_delete', $owner->associate_type),
            new UserAssociationEvent(
                [
                    'associate_id'   => $owner->getAnswer()->question_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function at(CommentEntity $owner)
    {
        $at_username = AtHelper::findAtUsername($owner->content);
        $at_user_ids = UserService::getUserIdByUsername($at_username);

        if ($at_user_ids) {
            //关联数据
            $associate_data = new AssociateDataModel();
            $associate_data->answer_id = $owner->associate_id;
            $associate_data->comment_id = $owner->id;

            //通知数据
            $notice_data = new NoticeDataModel();
            $notice_data->sender = $owner->created_by;
            $notice_data->receiver = $at_user_ids;

            //触发用户事件
            Yii::$app->user->trigger(
                sprintf('event_%s_at_sb', $owner->associate_type),
                new UserAssociationEvent(
                    [
                        'associate_id'   => $owner->getAnswer()->question_id,
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
        $comment = CommentService::getCommentByCommentId($owner->associate_id);
        $answer = AnswerService::getAnswerByAnswerId($comment->associate_id);

        //关联数据
        $associate_data = new AssociateDataModel();
        $associate_data->answer_id = $answer->id;
        $associate_data->comment_id = $owner->associate_id;

        //通知数据
        $notice_data = new NoticeDataModel();

        if ($owner->associate_type == AssociateModel::TYPE_ANSWER_COMMENT) {
            $notice_data->sender = $owner->created_by;
            $notice_data->receiver = $comment->created_by;
        }

        Yii::$app->user->trigger(
            sprintf('event_%s_vote', $owner->associate_type),
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

    public static function cancelVote(VoteEntity $owner)
    {
        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        //关联数据
        $comment = CommentService::getCommentByCommentId($owner->associate_id);
        $answer = AnswerService::getAnswerByAnswerId($comment->associate_id);

        $associate_type = AssociateModel::TYPE_QUESTION;
        $associate_id = $answer->question_id;
        $associate_data->answer_id = $answer->id;
        $associate_data->comment_id = $owner->associate_id;

        Yii::$app->user->trigger(
            sprintf('event_%s_cancel_vote', $owner->associate_type),
            new UserAssociationEvent(
                [
                    'associate_type' => $associate_type,
                    'associate_id'   => $associate_id,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }
}
