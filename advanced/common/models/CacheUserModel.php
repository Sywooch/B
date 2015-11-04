<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use Yii;

class CacheUserModel extends BaseCacheModel
{
    public $id;
    public $name;
    public $username;
    public $avatar;
    public $sex;
    public $title;
    public $bio;
    public $last_login_at;
}