<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use Yii;

class CacheTagModel extends BaseCacheModel
{
    public $id;
    public $name;
    public $alias;
    public $modify_at;
    public $count_follow = 0;
}