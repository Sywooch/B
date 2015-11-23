<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/23
 * Time: 10:28
 */

namespace common\behaviors;

use common\components\Counter;
use common\components\Notifier;
use common\entities\NotificationEntity;
use common\entities\PrivateMessageEntity;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class PrivateMessageDialogBehavior
 * @package common\behaviors
 * @property \common\entities\PrivateMessageDialogEntity owner
 */
class PrivateMessageDialogBehavior extends BaseBehavior
{

    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterPrivateMessageDialogInsert',
            //ActiveRecord::EVENT_AFTER_UPDATE  => 'afterPrivateMessageDialogUpdate',
            //ActiveRecord::EVENT_AFTER_DELETE  => 'afterPrivateMessageDialogDelete',
            //ActiveRecord::EVENT_BEFORE_UPDATE => 'beforePrivateMessageDialogSave',
        ];
    }

    public function afterPrivateMessageDialogInsert($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $this->dealWithUpdatePrivateMessage();
        $this->dealWithNotification();
        $this->dealWithCounter();
    }

    public function dealWithNotification()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        /* @var $private_message PrivateMessageEntity */
        $private_message = Yii::createObject(PrivateMessageEntity::className());
        $dialog_user_id = $private_message->getDialogUserId($this->owner->private_message_id, Yii::$app->user->id);

        $result = Notifier::build()->from(Yii::$app->user->id)->to($dialog_user_id)->notice(
            NotificationEntity::TYPE_PRIVATE_MESSAGE_TO_ME
        );

        return $result;
    }

    public function dealWithUpdatePrivateMessage()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = PrivateMessageEntity::updateLastActive(
            $this->owner->private_message_id,
            Yii::$app->user->id,
            $this->owner->content,
            $this->owner->create_at
        );

        Yii::trace(sprintf('updateLastActive: %s', $result), 'behavior');
    }

    public function dealWithCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = Counter::addPrivateMessage($this->owner->private_message_id);

        return $result;
    }
}
