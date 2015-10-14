<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/12
 * Time: 10:25
 */

namespace common\behaviors;

use common\entities\FollowQuestionEntity;
use common\entities\QuestionEntity;
use common\entities\TagEntity;
use common\services\NotificationService;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class QuestionContentBehavior
 * @package common\behaviors
 * @property \common\entities\QuestionEntity owner
 */
class TagBehavior extends Behavior
{
    /*public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');
        return [
                ActiveRecord::EVENT_AFTER_INSERT => 'afterQuestionInsert',
                ActiveRecord::EVENT_AFTER_UPDATE => 'afterQuestionUpdate',
                ActiveRecord::EVENT_AFTER_DELETE => 'afterQuestionDelete',
        ];
    }*/
}