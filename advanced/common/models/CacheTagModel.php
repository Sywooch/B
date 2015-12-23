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
    public $name = '';
    public $alias = '';
    public $icon = '';
    public $description = '';
    public $updated_at;
    public $count_follow = 0;
}