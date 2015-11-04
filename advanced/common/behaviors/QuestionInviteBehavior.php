<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/28
 * Time: 11:39
 */

namespace common\behaviors;


use common\components\Notifier;
use common\entities\NotificationEntity;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class QuestionInviteBehavior
 * @package common\behaviors
 * @property \common\entities\QuestionInviteEntity owner
 */
class QuestionInviteBehavior extends Behavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterQuestionInviteInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterQuestionInviteUpdate',
        ];
    }

    public function afterQuestionInviteInsert($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithNotifier();
    }

    public function afterQuestionInviteUpdate($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithNotifier();
    }

    private function dealWithNotifier()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = Notifier::build()->from($this->owner->create_by)->to($this->owner->invited_user_id)->set(
            NotificationEntity::TYPE_INVITE_ME_TO_ANSWER,
            $this->owner->question_id
        )->send();

        return $result;
    }
}