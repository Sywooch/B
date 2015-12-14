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
            ActiveRecord::EVENT_AFTER_INSERT => 'afterVoteInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'afterVoteUpdate',
            //ActiveRecord::EVENT_AFTER_DELETE => 'afterVoteDelete',
        ];
    }

    public function afterVoteInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        //
        $result = VoteService::addUserOfVoteCache(
            $this->owner->type,
            $this->owner->associate_id,
            $this->owner->created_by,
            $this->owner->vote
        );

        if ($result) {
            //todo 增加其他类型
            switch ($this->owner->type) {
                case VoteEntity::TYPE_QUESTION:
                    //更新问题投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::addQuestionLike($this->owner->associate_id);
                    } else {
                        Counter::addQuestionHate($this->owner->associate_id);
                    }
                    break;
                case VoteEntity::TYPE_ANSWER:
                    //更新回答投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::addAnswerLike($this->owner->associate_id);
                    } else {
                        Counter::addAnswerHate($this->owner->associate_id);
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
                $this->owner->type,
                $this->owner->associate_id,
                $this->owner->created_by,
                $this->owner->vote
            );

            if ($result) {
                //todo 增加其他类型
                switch ($this->owner->type) {
                    case VoteEntity::TYPE_QUESTION:
                        //更新问题投票数
                        if ($this->owner->vote == VoteEntity::VOTE_YES) {
                            Counter::addQuestionLike($this->owner->associate_id);
                            Counter::cancelQuestionHate($this->owner->associate_id);
                        } else {
                            Counter::addQuestionHate($this->owner->associate_id);
                            Counter::cancelQuestionLike($this->owner->associate_id);
                        }
                        break;
                    case VoteEntity::TYPE_ANSWER:
                        //更新回答投票数
                        if ($this->owner->vote == VoteEntity::VOTE_YES) {
                            Counter::addAnswerLike($this->owner->associate_id);
                            Counter::cancelAnswerHate($this->owner->associate_id);
                        } else {
                            Counter::addAnswerHate($this->owner->associate_id);
                            Counter::cancelAnswerLike($this->owner->associate_id);
                        }
                        break;
                }
            }
        }
    }

    /*public function afterVoteDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = VoteService::removeUserOfVoteCache(
            $this->owner->type,
            $this->owner->associate_id,
            $this->owner->created_by
        );

        if ($result) {
            //todo 增加其他类型
            switch ($this->owner->type) {
                case VoteEntity::TYPE_QUESTION:
                    //更新问题投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::cancelQuestionLike($this->owner->associate_id);
                    } else {
                        Counter::cancelQuestionHate($this->owner->associate_id);
                    }
                    break;
                case VoteEntity::TYPE_ANSWER:
                    //更新回答投票数
                    if ($this->owner->vote == VoteEntity::VOTE_YES) {
                        Counter::cancelAnswerLike($this->owner->associate_id);
                    } else {
                        Counter::cancelAnswerHate($this->owner->associate_id);
                    }
                    break;
            }
        }
    }*/
}
