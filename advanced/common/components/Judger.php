<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 16:07
 */

namespace common\components;

use yii\base\Object;

class Judger extends Object
{
    public static function checkNeedToJudge($user_id)
    {
        return true;
    }
}
