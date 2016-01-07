<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/5
 * Time: 19:36
 */

namespace common\exceptions;

use yii\base\Exception;

class UserEventDoesNotDefineException extends Exception
{
    public function __construct($event_name, $code = 500)
    {
        $message = sprintf('用户事件: %s 未定义！', $event_name);
        parent::__construct($message, $code);
    }
}
