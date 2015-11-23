<?php
/**
 * Description:
 * License:
 * User: Keen
 * Date: 2015/10/12
 * Time: 23:51
 * Version:
 * Created by PhpStorm.
 */

namespace common\exceptions;

use yii\base\Exception;

class ParamsInvalidException extends Exception
{
    public function __construct($params, $code = 500)
    {
        $message = sprintf('参数: %s 无效', is_array($params) ? implode(', ', $params) : $params);
        parent::__construct($message, $code);
    }
}
