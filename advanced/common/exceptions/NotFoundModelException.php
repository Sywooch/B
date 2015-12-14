<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/23
 * Time: 10:57
 */

namespace common\exceptions;

use yii\base\Exception;

class NotFoundModelException extends Exception
{
    public function __construct($model_name, $id, $code = 500)
    {
        $message = sprintf('%s[%s]不存在，或已被删除!', $model_name, $id);
        parent::__construct($message, $code);
    }
}
