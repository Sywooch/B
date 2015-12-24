<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/22
 * Time: 19:55
 */

namespace common\behaviors;

use common\components\Counter;
use common\services\FollowService;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class QuestionTagBehavior
 * @package common\behaviors
 * @property \common\entities\QuestionTagEntity owner
 */
class QuestionTagBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterQuestionTagInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterQuestionTagDelete',
        ];
    }

    public function afterQuestionTagInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        //更新标签使用数
        Counter::tagAddUse($this->owner->tag_id);
    }

    public function afterQuestionTagDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        //更新标签使用数
        Counter::tagCancelUse($this->owner->tag_id);
    }
}
