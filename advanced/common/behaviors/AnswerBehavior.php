<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/16
 * Time: 17:05
 */

namespace common\behaviors;


use common\components\Counter;
use common\components\Notifier;
use common\entities\AnswerVersionEntity;
use common\entities\FollowQuestionEntity;
use common\entities\FollowTagPassiveEntity;
use common\entities\NotificationEntity;
use common\entities\QuestionEntity;
use common\entities\TagEntity;
use common\entities\UserProfileEntity;
use Yii;
use yii\base\Behavior;

/**
 * Class AnswerBehavior
 * @package common\behaviors
 * @property \common\entities\AnswerEntity owner
 */
class AnswerBehavior extends Behavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterAnswerInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterAnswerUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterAnswerDelete',
        ];
    }

    public function afterAnswerInsert($event)
    {
        $this->dealWithNotification(NotificationEntity::TYPE_FOLLOW_QUESTION_HAS_NEW_ANSWER);
        $this->dealWithAddCounter();
        $this->dealWithUpdateQuestionActiveTime();
        $this->dealWithAddPassiveFollowTag();
    }

    public function afterAnswerUpdate($event)
    {
        $this->dealWithNotification(NotificationEntity::TYPE_FOLLOW_QUESTION_MODIFY_ANSWER);
        #not creator himself, create new version
        if (Yii::$app->user->id != $this->owner->create_by) {
            $this->dealWithNewAnswerVersion();
        }
        $this->dealWithUpdateQuestionActiveTime();
    }

    public function afterAnswerDelete($event)
    {
    }

    public function dealWithUpdateQuestionActiveTime()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        /* @var $questionEntity QuestionEntity */
        $questionEntity = Yii::createObject(QuestionEntity::className());
        $result = $questionEntity->updateActiveAt($this->owner->question_id, time());

        Yii::trace(sprintf('Update Active At: %s', $result), 'behavior');
    }

    public function dealWithAddCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = Counter::build()->set(
            UserProfileEntity::tableName(),
            $this->owner->create_by,
            'user_id'
        )->value(
            'count_answer',
            1
        )->execute();

        Yii::trace(sprintf('Counter: %s', $result), 'behavior');
    }

    public function dealWithNotification($type)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        /* @var $followQuestionEntity FollowQuestionEntity */
        $followQuestionEntity = Yii::createObject(FollowQuestionEntity::className());
        $user_ids = $followQuestionEntity->getFollowUserIds($this->owner->question_id);
        if ($user_ids) {
            $result = Notifier::build()->from($this->owner->create_by)->to($user_ids)->set(
                $type,
                $this->owner->question_id
            )->send();

            Yii::trace(sprintf('Notifier: %s', $result), 'behavior');
        }
    }

    public function dealWithNewAnswerVersion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        #check whether has exist version
        /* @var $answer_version_entity AnswerVersionEntity */
        $answer_version_entity = Yii::createObject(AnswerVersionEntity::className());
        $result = $answer_version_entity->addNewVersion($this->owner->id, $this->owner->content, $this->owner->reason);

        Yii::trace(sprintf('add New Version: %s', $result), 'behavior');
    }

    public function dealWithAddPassiveFollowTag()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $question = $this->owner->question;

        /* @var $tag_entity TagEntity */
        $tag_entity = Yii::createObject(TagEntity::className());
        $tag_ids = $tag_entity->getTagIdByName($question->tags);
        if ($tag_ids) {
            /* @var $follow_tag_passive_entity FollowTagPassiveEntity */
            $follow_tag_passive_entity = Yii::createObject(FollowTagPassiveEntity::className());
            $result = $follow_tag_passive_entity->addFollowTag(
                $this->owner->create_by,
                $tag_ids
            );

            Yii::trace(sprintf('Add Passive Follow Tag: %s', $result), 'behavior');
        }
    }
}