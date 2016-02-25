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
use common\events\AnswerEvent;
use common\events\CommentEvent;
use common\events\QuestionEvent;
use common\models\AssociateDataModel;
use common\models\AssociateModel;
use common\models\NoticeDataModel;
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

        $associate_data = new AssociateDataModel();
        $notice_data = new NoticeDataModel();

        if ($result) {
            //关联数据

            switch ($this->owner->associate_type) {
                case AssociateModel::TYPE_QUESTION:
                    //更新问题投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::questionAddLike($this->owner->associate_id);
                    } else {
                        Counter::questionAddHate($this->owner->associate_id);
                    }

                    //触发用户动作
                    QuestionEvent::vote($this->owner);

                    break;
                case AssociateModel::TYPE_ANSWER:
                    //更新回答投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::answerAddLike($this->owner->associate_id);
                    } else {
                        Counter::answerAddHate($this->owner->associate_id);
                    }

                    //触发用户动作
                    AnswerEvent::vote($this->owner);

                    break;
                case AssociateModel::TYPE_ANSWER_COMMENT:
                    //更新评论投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::answerCommentAddLike($this->owner->associate_id);
                    }

                    //触发用户动作
                    CommentEvent::vote($this->owner);

                    break;
                default:
                    throw new Exception(sprintf('暂不支持 %s insert 事件', $this->owner->associate_type));
                    break;
            }
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

            $associate_data = new AssociateDataModel();
            $notice_data = new NoticeDataModel();

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

                        //触发用户动作
                        QuestionEvent::vote($this->owner);

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

                        //触发用户动作
                        AnswerEvent::vote($this->owner);

                        break;
                    default:
                        throw new Exception(sprintf('暂不支持 %s update 事件', $this->owner->associate_type));
                        break;
                }
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

                //触发用户取消投票事件
                CommentEvent::cancelVote($this->owner);

                break;
            default:
                throw new Exception(sprintf('暂不支持 %s delete 事件', $this->owner->associate_type));
                break;
        }

        $this->deleteUserEventLog();
    }

    private function deleteUserEventLog()
    {
        //删除记录
        $event_name = sprintf('event_%s_vote', $this->owner->associate_type);
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
