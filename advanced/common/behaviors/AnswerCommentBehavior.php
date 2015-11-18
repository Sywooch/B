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
use common\entities\UserEntity;
use common\helpers\AtHelper;
use yii\base\Behavior;
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

    public function dealWithNotification()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $answer_data = AnswerEntity::getAnswerByAnswerId($this->owner->answer_id);
        if ($answer_data && isset($answer_data['create_by'])) {
            Notifier::build()->from($this->owner->create_by)->to($answer_data['create_by'])->notice(
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
    public function dealWithAt()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $username = AtHelper::findAtUsername($this->owner->content);

        $user_ids = UserEntity::getUserIdByUsername($username);

        Notifier::build()->from(Yii::$app->user->id)->to($user_ids)->notice(
            NotificationEntity::TYPE_COMMENT_AT_ME,
            [
                'user_id' => $this->owner->id,
            ]
        );
    }

    public function dealWithCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        Counter::addAnswerComment($this->owner->answer_id);
    }
}