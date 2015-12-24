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
 * Class FollowUserBehavior
 * @package common\behaviors
 * @property \common\entities\FollowUserEntity owner
 */
class FollowUserBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterFollowUserInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterFollowUserDelete',
        ];
    }

    public function afterFollowUserInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = FollowService::addUserFansCache($this->owner->follow_user_id, $this->owner->user_id);

        Yii::trace('增加关注此用户的用户缓存:' . var_export($result, true), 'behavior');

        if ($result) {
            //关注用户后
            Counter::userAddFollowUser($this->owner->user_id);
            Counter::userAddFans($this->owner->follow_user_id);
            //todo
        }
    }

    public function afterFollowUserDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = FollowService::removeUserFansCache(
            $this->owner->follow_user_id,
            $this->owner->user_id
        );

        Yii::trace('取消关注此用户的用户缓存:' . var_export($result, true), 'behavior');

        if ($result) {
            //取消关注用户后
            Counter::userCancelFollowTag($this->owner->user_id);
            Counter::userCancelFans($this->owner->follow_user_id);
            //todo
        }
    }
}
