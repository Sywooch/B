<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/2
 * Time: 17:29
 */

namespace common\components\user;

use yii\base\Object;

class BaseRole extends Object
{
    public $name;

    public function __construct($name, $config = null)
    {
        parent::__construct($config);
        $this->name = $name;
    }
}
