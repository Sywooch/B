<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/13
 * Time: 9:36
 */

namespace common\behaviors;

use common\components\Counter;
use common\services\FollowService;
use Yii;
use yii\db\ActiveRecord;

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
            ActiveRecord::EVENT_AFTER_INSERT => 'afterFollowQuestionInsert',
        ];
    }

    public function afterFollowQuestionInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithCounter();
        $this->dealWithUserWhoFollowQuestion();

    }

    /**
     * 关注此问题的人
     */
    private function dealWithUserWhoFollowQuestion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        FollowService::addUserWhoFollowQuestion($this->owner->follow_question_id, $this->owner->user_id);
    }

    private function dealWithCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        Counter::followQuestion($this->owner->user_id);
        Counter::addQuestionFollow($this->owner->follow_question_id);
    }
}
