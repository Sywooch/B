<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/6
 * Time: 19:57
 */

namespace common\components\user;

use common\models\NoticeDataModel;
use yii\base\Event;

/**
 * Class UserAssociationEvent
 * @package common\components\user
 * @property array           $event_data
 * @property NoticeDataModel $notice_data
 */
class UserAssociationEvent extends Event
{
    public $associate_type; //类型　AssociateModel下的相关类型
    public $associate_id;//ID
    public $associate_data = [];//关联数据
    public $notice_data = [];//通知数据
}
