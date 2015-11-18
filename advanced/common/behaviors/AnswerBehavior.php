<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/16
 * Time: 17:05
 */

namespace common\behaviors;


use common\components\Counter;
use common\components\Error;
use common\components\Notifier;
use common\entities\AnswerVersionEntity;
use common\entities\FollowQuestionEntity;
use common\entities\FollowTagPassiveEntity;
use common\entities\NotificationEntity;
use common\entities\QuestionEntity;
use common\entities\TagEntity;
use common\helpers\StringHelper;
use common\helpers\TimeHelper;
use common\models\CacheAnswerModel;
use Yii;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;

/**
 * Class AnswerBehavior
 * @package common\behaviors
 * @property \common\entities\AnswerEntity owner
 */
class AnswerBehavior extends BaseBehavior
{
    const NEED_NOTIFICATION_ANSWER_CONTENT_LENGTH = 20;

    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');
        
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeAnswerInsert',
            ActiveRecord::EVENT_AFTER_INSERT  => 'afterAnswerInsert',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'afterAnswerUpdate',
            ActiveRecord::EVENT_AFTER_DELETE  => 'afterAnswerDelete',
        ];
    }
    
    public function beforeAnswerInsert(ModelEvent $event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $owner = $this->owner;
        $hasAnswered = $owner->checkWhetherHasAnswered($owner->question_id, $owner->create_by);
        
        $owner->is_anonymous = $owner->is_anonymous == 1 ? $owner::STATUS_ANONYMOUS : $owner::STATUS_UNANONYMOUS;

        if ($hasAnswered) {
            $event->isValid = false;
            $event->sender->addError('question_id', '一个问题只能回答一次。');
            
            return Error::set(Error::TYPE_ANSWER_ONE_QUESTION_ONE_ANSWER_PER_PEOPLE);
        }
    }
    
    public function afterAnswerInsert($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithNotification(NotificationEntity::TYPE_FOLLOW_QUESTION_HAS_NEW_ANSWER);
        $this->dealWithAddCounter();
        $this->dealWithUpdateQuestionActiveTime();
        $this->dealWithAddPassiveFollowTag();
        $this->dealWithCache();
    }

    public function dealWithCache()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        #marked user has answered this question
        $cache_key = [
            REDIS_KEY_QUESTION_HAS_ANSWERED,
            implode(':', [$this->owner->create_by, $this->owner->question_id]),
        ];
        Yii::$app->redis->set($cache_key, $this->owner->id);

        #add answer to list
        Yii::$app->redis->zAdd(
            [REDIS_KEY_ANSWER_LIST_TIME, $this->owner->question_id],
            TimeHelper::getCurrentTime(),
            $this->owner->id
        );
        Yii::$app->redis->zAdd([REDIS_KEY_ANSWER_LIST_SCORE, $this->owner->question_id], 0, $this->owner->id);

        $item = (new CacheAnswerModel())->filterAttributes($this->owner->getAttributes());

        Yii::$app->redis->HMSET([REDIS_KEY_ANSWER, $this->owner->id], $item);
    }
    
    public function afterAnswerUpdate($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithNotification(NotificationEntity::TYPE_FOLLOW_QUESTION_MODIFY_ANSWER);
        #不是本人，或本人，但创建时间已超过 ? 天
        if (Yii::$app->user->id != $this->owner->create_by || $this->owner->create_at <= TimeHelper::getBeforeTime(1)) {
            $this->dealWithNewAnswerVersion();
        }
        $this->dealWithUpdateQuestionActiveTime();
    }
    
    public function afterAnswerDelete($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }
    
    public function dealWithCheckWhetherHasAnswered()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }
    
    public function dealWithUpdateQuestionActiveTime()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        
        /* @var $questionEntity QuestionEntity */
        $questionEntity = Yii::createObject(QuestionEntity::className());
        $result = $questionEntity->updateActiveAt($this->owner->question_id, TimeHelper::getCurrentTime());
        
        Yii::trace(sprintf('Update Active At: %s', $result), 'behavior');
    }
    
    public function dealWithAddCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        Counter::addAnswer($this->owner->create_by);
        Counter::addQuestionAnswer($this->owner->question_id);
    }
    
    public function dealWithNotification($type)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        #notification where answer is enough long.
        if (StringHelper::countStringLength($this->owner->content) >= self::NEED_NOTIFICATION_ANSWER_CONTENT_LENGTH) {
            /* @var $followQuestionEntity FollowQuestionEntity */
            $followQuestionEntity = Yii::createObject(FollowQuestionEntity::className());
            $user_ids = $followQuestionEntity->getFollowUserIds($this->owner->question_id);
            if ($user_ids) {
                Notifier::build()->from($this->owner->create_by)->to($user_ids)->notice(
                    $type,
                    ['question_id' => $this->owner->question_id]
                );
            }
        }
    }
    
    public function dealWithNewAnswerVersion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        #check whether has exist version
        /* @var $answer_version_entity AnswerVersionEntity */
        $answer_version_entity = Yii::createObject(AnswerVersionEntity::className());
        $result = $answer_version_entity->addNewVersion($this->owner->id, $this->owner->content, $this->owner->reason);
        
        if ($result) {
            #count_common_edit
            Counter::addCommonEdit(Yii::$app->user->id);
        }
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
            $tag_ids = is_array($tag_ids) ? $tag_ids : [$tag_ids];
            $result = FollowTagPassiveEntity::addFollowTag(
                $this->owner->create_by,
                $tag_ids
            );
            
            Yii::trace(sprintf('Add Passive Follow Tag: %s', $result), 'behavior');
        }
    }
}