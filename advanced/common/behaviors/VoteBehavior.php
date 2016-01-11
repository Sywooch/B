<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/14
 * Time: 15:59
 */

namespace common\behaviors;

use common\components\Counter;
use common\entities\VoteEntity;
use common\services\VoteService;
use Yii;
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

        //
        $result = VoteService::addUserOfVoteCache(
            $this->owner->associate_type,
            $this->owner->associate_id,
            $this->owner->created_by,
            $this->owner->vote
        );

        if ($result) {
            //todo 增加其他类型
            switch ($this->owner->associate_type) {
                case VoteEntity::TYPE_QUESTION:
                    //更新问题投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::questionAddLike($this->owner->associate_id);
                    } else {
                        Counter::questionAddHate($this->owner->associate_id);
                    }
                    break;
                case VoteEntity::TYPE_ANSWER:
                    //更新回答投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::answerAddLike($this->owner->associate_id);
                    } else {
                        Counter::answerAddHate($this->owner->associate_id);
                    }
                    break;
                case VoteEntity::TYPE_ANSWER_COMMENT:
                    //更新回答投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::answerCommentAddLike($this->owner->associate_id);
                    }
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

            if ($result) {
                //todo 增加其他类型
                switch ($this->owner->associate_type) {
                    case VoteEntity::TYPE_QUESTION:
                        //更新问题投票数
                        if ($this->owner->vote == VoteEntity::VOTE_YES) {
                            Counter::questionAddLike($this->owner->associate_id);
                            Counter::questionCancelHate($this->owner->associate_id);
                        } else {
                            Counter::questionAddHate($this->owner->associate_id);
                            Counter::questionCancelLike($this->owner->associate_id);
                        }
                        break;
                    case VoteEntity::TYPE_ANSWER:
                        //更新回答投票数
                        if ($this->owner->vote == VoteEntity::VOTE_YES) {
                            Counter::answerAddLike($this->owner->associate_id);
                            Counter::answerCancelHate($this->owner->associate_id);
                        } else {
                            Counter::answerAddHate($this->owner->associate_id);
                            Counter::answerCancelLike($this->owner->associate_id);
                        }
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
            case VoteEntity::TYPE_ANSWER_COMMENT:
                //更新回答投票数
                Counter::answerCommentCancelLike($this->owner->associate_id);
                break;
        }
    }

}
