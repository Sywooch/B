<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/13
 * Time: 9:36
 */

namespace common\behaviors;


use common\services\NotificationService;
use Yii;
use yii\base\Behavior;

/**
 * Class FollowQuestionBehavior
 * @package common\behaviors
 * @property \common\entities\FollowQuestionEntity owner
 */
class FollowQuestionBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            //ActiveRecord::EVENT_AFTER_DELETE => 'afterRemoveFollowQuestion',
        ];
    }

}