<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/12
 * Time: 10:25
 */

namespace common\behaviors;

use Yii;

/**
 * Class QuestionContentBehavior
 * @package common\behaviors
 * @property \common\entities\QuestionEntity owner
 */
class TagBehavior extends BaseBehavior
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
