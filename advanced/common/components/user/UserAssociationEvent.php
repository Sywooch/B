<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/6
 * Time: 19:57
 */

namespace common\components\user;

use yii\base\Event;

class UserAssociationEvent extends Event
{
    public $type;
    public $id;
    public $content;
}
