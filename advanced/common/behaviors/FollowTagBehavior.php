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
 * Class FollowTagBehavior
 * @package common\behaviors
 * @property \common\entities\FollowTagEntity owner
 */
class FollowTagBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterFollowTagInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterFollowTagDelete',
        ];
    }

    public function afterFollowTagInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = FollowService::addUserOfFollowTagCache($this->owner->follow_tag_id, $this->owner->user_id);

        Yii::trace('增加关注此标签的用户缓存:' . var_export($result, true), 'behavior');

        if ($result) {
            //关注问题后，更新标签被关注的数量
            Counter::userAddFollowTag($this->owner->user_id);
            Counter::tagAddFollow($this->owner->follow_tag_id);
        }
    }

    public function afterFollowTagDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = FollowService::removeUserOfFollowTagCache(
            $this->owner->follow_tag_id,
            $this->owner->user_id
        );

        Yii::trace('移除关注此标签的用户缓存:' . var_export($result, true), 'behavior');

        if ($result) {
            //关注问题后，更新标签被关注的数量
            Counter::userCancelFollowTag($this->owner->user_id);
            Counter::tagCancelFollow($this->owner->follow_tag_id);
        }
    }
}