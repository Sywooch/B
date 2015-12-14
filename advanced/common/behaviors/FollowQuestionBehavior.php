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
            ActiveRecord::EVENT_AFTER_DELETE => 'afterFollowQuestionDelete',
        ];
    }

    public function afterFollowQuestionInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        //关注此问题的人，更新缓存，此方法顺序必须比 dealWithCounter 先执行
        $result = FollowService::addUserOfFollowQuestionCache($this->owner->follow_question_id, $this->owner->user_id);

        if ($result) {
            //关注问题后，更新问题被关注的数量
            Counter::addUserFollowQuestion($this->owner->user_id);
            Counter::addQuestionFollow($this->owner->follow_question_id);
        }
    }

    public function afterFollowQuestionDelete()
    {
        //关注此问题的人，更新缓存，此方法顺序必须比 dealWithCounter 先执行
        $result = FollowService::removeUserOfFollowQuestionCache(
            $this->owner->follow_question_id,
            $this->owner->user_id
        );

        if ($result) {
            //关注问题后，更新问题被关注的数量
            Counter::cancelUserFollowQuestion($this->owner->user_id);
            Counter::cancelQuestionFollow($this->owner->follow_question_id);
        }
    }
}
