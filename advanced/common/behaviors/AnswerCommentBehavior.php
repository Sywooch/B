<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 19:12
 */

namespace common\behaviors;


use common\components\Notifier;
use common\entities\NotificationEntity;
use common\entities\UserEntity;
use common\helpers\AtHelper;
use yii\base\Behavior;
use Yii;

/**
 * Class AnswerCommentBehavior
 * @package common\behaviors
 * @property \common\entities\AnswerCommentEntity owner
 */
class AnswerCommentBehavior extends Behavior
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
    }

    public function dealWithNotification()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = Notifier::build()->from(Yii::$app->user->id)->to()->set(
            NotificationEntity::TYPE_MY_ANSWER_HAS_NEW_COMMENT
        )->send();

        Yii::trace(sprintf('Notifier: %s', $result), 'behavior');
    }

    /**
     * todo untest
     * @throws \yii\base\InvalidConfigException
     */
    public function dealWithAt()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        /* @var $user_entity UserEntity */
        $user_entity = Yii::createObject(UserEntity::className());
        $username = AtHelper::findAtUsername($this->owner->content);

        $user_ids = $user_entity->getUserIdByUsername($username);

        $result = Notifier::build()->from(Yii::$app->user->id)->to($user_ids)->set(
            NotificationEntity::TYPE_COMMENT_AT_ME,
            $this->owner->id
        );

        Yii::trace(sprintf('Notifier: %s', $result), 'behavior');
    }
}