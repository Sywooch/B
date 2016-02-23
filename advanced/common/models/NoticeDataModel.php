<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-02-16
 * Time: 17:16
 */

namespace common\models;

use common\entities\NotificationEntity;
use yii\base\Model;

class NoticeDataModel extends Model
{
    public $sender;
    public $receiver;
    public $associate_type;
    public $associate_id;
    public $associate_data;
    public $user_event_id;
    public $level = NotificationEntity::LEVEL_NORMAL;
}
