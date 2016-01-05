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
use common\components\Updater;
use common\config\RedisKey;
use common\entities\AnswerCommentEntity;
use common\entities\AnswerVersionEntity;
use common\entities\NotificationEntity;
use common\helpers\StringHelper;
use common\helpers\TimeHelper;
use common\models\CacheAnswerModel;
use common\services\AnswerService;
use common\services\FollowService;
use common\services\NotificationService;
use common\services\TagService;
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
        $hasAnswered = AnswerService::checkWhetherHasAnswered($owner->question_id, $owner->created_by);
        
        $owner->is_anonymous = $owner->is_anonymous == 1 ? $owner::STATUS_ANONYMOUS : $owner::STATUS_UNANONYMOUS;

        if ($hasAnswered) {
            $event->isValid = false;
            $event->sender->addError('question_id', '一个问题只能回答一次。');
            
            return Error::set(Error::TYPE_ANSWER_ONE_QUESTION_ONE_ANSWER_PER_PEOPLE);
        }

        return true;
    }
    
    public function afterAnswerInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //通知
        $this->dealWithNotification(NotificationService::TYPE_FOLLOW_QUESTION_HAS_NEW_ANSWER);

        //关注问题，此方法必须在 dealWithAddCounter 前
        $this->dealWithAddQuestionFollow();

        //更新问题的活跃时间
        $this->dealWithUpdateQuestionActiveTime();

        //更新被动TAG
        $this->dealWithAddPassiveFollowTag();

        //处理回答缓存
        $this->dealWithAnswerInsertCache();

        //计数
        $this->dealWithAddCounter();
    }

    
    public function afterAnswerUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithNotification(NotificationService::TYPE_FOLLOW_QUESTION_MODIFY_ANSWER);
        #不是本人，或本人，但创建时间已超过 ? 天
        if (Yii::$app->user->id != $this->owner->created_by || $this->owner->created_at <= TimeHelper::getBeforeTime(
                1
            )
        ) {
            $this->dealWithNewAnswerVersion();
        }
        $this->dealWithUpdateQuestionActiveTime();
        $this->dealWithUpdateAnswerCache();
    }
    
    public function afterAnswerDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //处理回答缓存
        $this->dealWithAnswerDeleteCache();
        //计数
        $this->dealWithReduceCounter();
        //删除回答评论
        $this->dealWithAnswerCommentDelete();
    }


    private function dealWithAddQuestionFollow()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        FollowService::addFollowQuestion($this->owner->question_id, $this->owner->created_by);
    }

    private function dealWithAnswerInsertCache()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        #marked user has answered this question
        $cache_key = [
            RedisKey::REDIS_KEY_QUESTION_HAS_ANSWERED,
            implode(':', [$this->owner->created_by, $this->owner->question_id]),
        ];
        Yii::$app->redis->set($cache_key, $this->owner->id);

        #add answer to list
        Yii::$app->redis->zAdd(
            [
                RedisKey::REDIS_KEY_ANSWER_LIST_TIME,
                $this->owner->question_id,
            ],
            TimeHelper::getCurrentTime(),
            $this->owner->id
        );

        Yii::$app->redis->zAdd([RedisKey::REDIS_KEY_ANSWER_LIST_SCORE, $this->owner->question_id], 0, $this->owner->id);

        $item = (new CacheAnswerModel())->filterAttributes($this->owner->getAttributes());

        AnswerService::updateAnswerCache($this->owner->id, $item);
    }

    private function dealWithAnswerDeleteCache()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        //清除是否已回答缓存
        $cache_key_answered = [
            RedisKey::REDIS_KEY_QUESTION_HAS_ANSWERED,
            implode(':', [$this->owner->created_by, $this->owner->question_id]),
        ];
        if (Yii::$app->redis->get($cache_key_answered)) {
            Yii::$app->redis->delete($cache_key_answered);
        }

        //清除按时间回答列表缓存
        $cache_key_time = [RedisKey::REDIS_KEY_ANSWER_LIST_TIME, $this->owner->question_id,];
        if (Yii::$app->redis->zScore($cache_key_time, $this->owner->id) !== false) {
            Yii::$app->redis->zRem($cache_key_time, $this->owner->id);
        }

        //清除按分数回答列表缓存
        $cache_key_score = [RedisKey::REDIS_KEY_ANSWER_LIST_SCORE, $this->owner->question_id,];
        if (Yii::$app->redis->zScore($cache_key_score, $this->owner->id) !== false) {
            Yii::$app->redis->zRem($cache_key_score, $this->owner->id);
        }
    }
    
    private function dealWithCheckWhetherHasAnswered()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }
    
    private function dealWithUpdateQuestionActiveTime()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = Updater::updateQuestionActiveAt($this->owner->question_id, TimeHelper::getCurrentTime());
        
        Yii::trace(sprintf('Update Active At: %s', var_export($result, true)), 'behavior');
    }
    
    private function dealWithAddCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        Counter::userAddAnswer($this->owner->created_by);
        Counter::questionAddAnswer($this->owner->question_id);
    }

    private function dealWithReduceCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        Counter::userDeleteAnswer($this->owner->created_by);
        Counter::questionDeleteAnswer($this->owner->question_id);
    }
    
    private function dealWithNotification($type)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        #notification where answer is enough long.
        if (StringHelper::countStringLength($this->owner->content) >= self::NEED_NOTIFICATION_ANSWER_CONTENT_LENGTH) {
            $user_ids = FollowService::getFollowQuestionUserIdsByQuestionId($this->owner->question_id);

            if ($user_ids) {
                Notifier::build()->from($this->owner->created_by)->to($user_ids)->notice(
                    $type,
                    ['question_id' => $this->owner->question_id]
                );
            }
        }
    }
    
    private function dealWithNewAnswerVersion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        #check whether has exist version
        $result = AnswerService::addNewVersion($this->owner->id, $this->owner->content, $this->owner->reason);
        
        if ($result && $this->owner->created_by != Yii::$app->user->id) {
            #count_common_edit
            Counter::userAddCommonEdit(Yii::$app->user->id);
        }
        Yii::trace(sprintf('add New Version: %s', var_export($result, true)), 'behavior');
    }
    
    private function dealWithAddPassiveFollowTag()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        
        $question = $this->owner->question;

        $tag_ids = TagService::getTagIdByName($question->tags);
        
        if ($tag_ids) {
            $tag_ids = is_array($tag_ids) ? $tag_ids : [$tag_ids];
            $result = FollowService::addFollowTagPassive($this->owner->created_by, $tag_ids);
            
            Yii::trace(sprintf('Add Passive Follow Tag: %s', var_export($result, true)), 'behavior');
        }
    }

    private function dealWithUpdateAnswerCache()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        AnswerService::updateAnswerCache(
            $this->owner->id,
            [
                'content'    => $this->owner->content,
                'updated_at' => TimeHelper::getCurrentTime(),
                'updated_by' => Yii::$app->user->id,
            ]
        );
    }

    private function dealWithAnswerCommentDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $answer_comments = AnswerCommentEntity::find()->where(
            [
                'answer_id' => $this->owner->id,
            ]
        )->all();

        foreach ($answer_comments as $answer_comment) {
            $answer_comment->delete();
        }
    }
}
