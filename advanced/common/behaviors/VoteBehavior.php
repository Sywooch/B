<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/14
 * Time: 15:59
 */

namespace common\behaviors;

use common\components\Counter;
use common\components\Notifier;
use common\components\user\UserAssociationEvent;
use common\entities\UserEventLogEntity;
use common\entities\VoteEntity;
use common\models\AssociateModel;
use common\services\AnswerService;
use common\services\CommentService;
use common\services\NotificationService;
use common\services\QuestionService;
use common\services\UserEventService;
use common\services\VoteService;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * Class VoteBehavior
 * @package common\behaviors
 * @property \common\entities\VoteEntity owner
 */
class VoteBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'afterVoteInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'afterVoteUpdate',
            ActiveRecord::EVENT_AFTER_DELETE  => 'afterVoteDelete',
        ];
    }

    public function afterVoteInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = VoteService::addUserOfVoteCache(
            $this->owner->associate_type,
            $this->owner->associate_id,
            $this->owner->created_by,
            $this->owner->vote
        );

        if ($result) {
            //关联数据
            $associate_data = [];

            switch ($this->owner->associate_type) {
                case AssociateModel::TYPE_QUESTION:
                    //更新问题投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::questionAddLike($this->owner->associate_id);
                    } else {
                        Counter::questionAddHate($this->owner->associate_id);
                    }
                    //赞问题通知
                    $question = QuestionService::getQuestionByQuestionId($this->owner->associate_id);
                    Notifier::build()->from($this->owner->created_by)
                            ->to($question->created_by)
                            ->where(
                                [
                                    AssociateModel::TYPE_QUESTION,
                                    $this->owner->associate_id,
                                ],
                                ['question_id' => $this->owner->associate_id]
                            )->notice(NotificationService::TYPE_QUESTION_BE_AGREED);
                    break;
                case AssociateModel::TYPE_ANSWER:
                    //更新回答投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::answerAddLike($this->owner->associate_id);
                    } else {
                        Counter::answerAddHate($this->owner->associate_id);
                    }

                    //关联数据
                    $answer = AnswerService::getAnswerByAnswerId($this->owner->associate_id);
                    $associate_data = [
                        'question_id' => $answer->question_id,
                    ];

                    //赞问题通知
                    Notifier::build()->from($this->owner->created_by)
                            ->to($answer->created_by)
                            ->where(
                                [
                                    AssociateModel::TYPE_QUESTION,
                                    $answer->question_id,
                                ],
                                [
                                    'question_id' => $answer->question_id,
                                    'answer_id'   => $this->owner->associate_id,
                                ]
                            )->notice(NotificationService::TYPE_ANSWER_BE_AGREED);
                    break;
                case AssociateModel::TYPE_ANSWER_COMMENT:
                    //更新评论投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::answerCommentAddLike($this->owner->associate_id);
                    }

                    //关联数据
                    $comment = CommentService::getCommentByCommentId($this->owner->associate_id);
                    $answer = AnswerService::getAnswerByAnswerId($comment->associate_id);
                    $associate_data = [
                        'question_id' => $answer->question_id,
                        'answer_id'   => $answer->id,
                    ];

                    //赞评论通知
                    $comment = CommentService::getCommentByCommentId($this->owner->associate_id);

                    $question_id = $comment->getAnswer()->question_id;
                    Notifier::build()->from($this->owner->created_by)
                            ->to($comment->created_by)
                            ->where(
                                [
                                    AssociateModel::TYPE_QUESTION,
                                    $question_id,
                                ],
                                [
                                    'question_id' => $question_id,
                                    'answer_id'   => $comment->associate_id,
                                    'comment_id'  => $this->owner->associate_id,
                                ]
                            )->notice(NotificationService::TYPE_COMMENT_BE_AGREED_IN_ANSWER);
                    break;
                default:
                    throw new Exception(sprintf('暂不支持 %s insert 事件', $this->owner->associate_type));
                    break;
            }

            //触发用户事件
            $this->triggerUserVoteEvent($associate_data);
        }
    }

    public function afterVoteUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        if ($this->owner->vote != $this->owner->getOldAttribute('vote')) {
            //增加新缓存
            $result = VoteService::addUserOfVoteCache(
                $this->owner->associate_type,
                $this->owner->associate_id,
                $this->owner->created_by,
                $this->owner->vote
            );

            if ($result) {
                switch ($this->owner->associate_type) {
                    case AssociateModel::TYPE_QUESTION:
                        //更新问题投票数
                        if ($this->owner->vote == VoteEntity::VOTE_YES) {
                            Counter::questionAddLike($this->owner->associate_id);
                            Counter::questionCancelHate($this->owner->associate_id);
                        } else {
                            Counter::questionAddHate($this->owner->associate_id);
                            Counter::questionCancelLike($this->owner->associate_id);
                        }

                        //关联数据
                        $associate_data = [];

                        break;
                    case AssociateModel::TYPE_ANSWER:
                        //更新回答投票数
                        if ($this->owner->vote == VoteEntity::VOTE_YES) {
                            Counter::answerAddLike($this->owner->associate_id);
                            Counter::answerCancelHate($this->owner->associate_id);
                        } else {
                            Counter::answerAddHate($this->owner->associate_id);
                            Counter::answerCancelLike($this->owner->associate_id);
                        }

                        //关联数据
                        $answer = AnswerService::getAnswerByAnswerId($this->owner->associate_id);
                        $associate_data = [
                            'question_id' => $answer->question_id,
                        ];

                        break;
                    default:
                        throw new Exception(sprintf('暂不支持 %s update 事件', $this->owner->associate_type));
                        break;
                }
                //触发用户事件
                $this->triggerUserVoteEvent($associate_data);
            }
        }
    }

    /**
     * 只有评论投票才有删除动作
     * @throws \Exception
     * @throws \common\exceptions\NotFoundModelException
     */
    public function afterVoteDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        switch ($this->owner->associate_type) {
            case AssociateModel::TYPE_ANSWER_COMMENT:
                //更新回答投票数
                Counter::answerCommentCancelLike($this->owner->associate_id);

                //关联数据
                $comment = CommentService::getCommentByCommentId($this->owner->associate_id);
                $answer = AnswerService::getAnswerByAnswerId($comment->associate_id);
                $associate_data = [
                    'question_id' => $answer->question_id,
                    'answer_id'   => $answer->id,
                ];
                break;
            default:
                throw new Exception(sprintf('暂不支持 %s delete 事件', $this->owner->associate_type));
                break;
        }

        $this->triggerUserCancelVoteEvent($associate_data);
    }

    private function triggerUserVoteEvent(array $associate_data)
    {
        $associate_data['template'] = [($this->owner->vote == VoteEntity::VOTE_YES) ? '推荐' : '反对'];
        //触发用户投票事件
        $event_name = sprintf('event_%s_vote', $this->owner->associate_type);

        Yii::$app->user->trigger(
            $event_name,
            new UserAssociationEvent(
                [
                    'type' => $this->owner->associate_type,
                    'id'   => $this->owner->associate_id,
                    'data' => $associate_data,
                ]
            )
        );
    }

    private function triggerUserCancelVoteEvent($associate_data)
    {
        //触发用户取消投票事件
        $event_name = sprintf('event_%s_vote', $this->owner->associate_type);
        $cancel_event_name = sprintf('event_%s_cancel_vote', $this->owner->associate_type);

        Yii::$app->user->trigger(
            $cancel_event_name,
            new UserAssociationEvent(
                [
                    'type' => $this->owner->associate_type,
                    'id'   => $this->owner->associate_id,
                    'data' => $associate_data,
                ]
            )
        );

        //删除记录
        $event = UserEventService::getUserEventByEventName($event_name);
        $user_event_log = UserEventLogEntity::find()->where(
            [
                'user_event_id'  => $event->id,
                'associate_type' => $this->owner->associate_type,
                'associate_id'   => $this->owner->associate_id,
                'created_by'     => $this->owner->created_by,

            ]
        )->one();
        if ($user_event_log) {
            $user_event_log->delete();
        }
    }
}
