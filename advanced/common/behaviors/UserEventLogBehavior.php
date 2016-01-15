<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/14
 * Time: 21:10
 */

namespace common\behaviors;


use common\entities\UserEventLogEntity;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class UserEventLogBehavior
 * @package common\behaviors
 * @property UserEventLogEntity owner
 */
class UserEventLogBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_FIND      => 'afterFind',
        ];
    }

    public function beforeValidate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->owner->associate_data = Json::encode($this->owner->associate_data);
    }

    public function afterFind()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->owner->associate_data = Json::decode($this->owner->associate_data);
    }
}