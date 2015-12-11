<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 19:12
 */

namespace common\behaviors;


use common\components\Counter;
use common\components\Notifier;
use common\entities\AnswerEntity;
use common\entities\NotificationEntity;
use common\helpers\AtHelper;
use common\services\AnswerService;
use common\services\UserService;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class AnswerCommentBehavior
 * @package common\behaviors
 * @property \common\entities\AnswerCommentEntity owner
 */
class AnswerCommentBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterAnswerCommentInsert',
        ];
    }
    
    public function afterAnswerCommentInsert($event)
    {
        $this->dealWithNotification();
        $this->dealWithAt();
        $this->dealWithCounter();
    }

    private function dealWithNotification()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $answer_data = AnswerService::getAnswerByAnswerId($this->owner->answer_id);
        if ($answer_data && isset($answer_data['created_by'])) {
            Notifier::build()->from($this->owner->created_by)->to($answer_data['created_by'])->notice(
                NotificationEntity::TYPE_MY_ANSWER_HAS_NEW_COMMENT,
                [
                    'question_id' => $this->owner->answer_id,
                    'answer_id'   => $answer_data['id'],
                ]
            );
        }
    }

    /**
     * todo untest
     * @throws \yii\base\InvalidConfigException
     */
    private function dealWithAt()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $username = AtHelper::findAtUsername($this->owner->content);

        if($username){
            $user_ids = UserService::getUserIdByUsername($username);

            Notifier::build()->from(Yii::$app->user->id)->to($user_ids)->notice(
                NotificationEntity::TYPE_COMMENT_AT_ME,
                [
                    'user_id' => $this->owner->id,
                ]
            );
        }
    }

    private function dealWithCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        Counter::addAnswerComment($this->owner->answer_id);
    }
}