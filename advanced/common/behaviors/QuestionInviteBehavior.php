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
use common\events\QuestionEvent;
use common\models\AssociateModel;
use common\services\NotificationService;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class QuestionInviteBehavior
 * @package common\behaviors
 * @property \common\entities\QuestionInviteEntity owner
 */
class QuestionInviteBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterQuestionInviteInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterQuestionInviteUpdate',
        ];
    }

    public function afterQuestionInviteInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //触发用户动作
        QuestionEvent::invite($this->owner);
    }

    public function afterQuestionInviteUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithNotifier();
    }
    }
