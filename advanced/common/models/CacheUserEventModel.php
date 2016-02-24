<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use Yii;

class CacheUserEventModel extends BaseCacheModel
{
    public $id;
    public $name;
    public $event;
    public $need_record = 'yes';
    public $event_template;
    public $need_notice = 'no';
    public $notice_template;
}
