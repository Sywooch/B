<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/16
 * Time: 17:05
 */

namespace common\behaviors;

use Yii;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;

/**
 * Class UserBehavior
 * @package common\behaviors
 * @property \common\entities\UserEntity owner
 */
class UserBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');
        
        return [
            //ActiveRecord::EVENT_BEFORE_INSERT => 'beforeUserInsert',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterUserInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUserUpdate',
            //ActiveRecord::EVENT_AFTER_DELETE  => 'afterUserDelete',
        ];
    }

    public function afterUserInsert($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithCacheInsert();
    }

    public function afterUserUpdate($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithCacheUpdate();
    }

    public function dealWithCacheInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }

    public function dealWithCacheUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }


}