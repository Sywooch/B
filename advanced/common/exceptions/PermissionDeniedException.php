<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/27
 * Time: 11:12
 */

namespace common\exceptions;

use yii\web\ForbiddenHttpException;

class PermissionDeniedException extends ForbiddenHttpException
{
    public function __construct($message= null, $code = 403)
    {
        $message = $message? $message : '您当前没有权限操作此项！';
        parent::__construct($message, $code);
    }
}