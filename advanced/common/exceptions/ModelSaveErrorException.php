<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/7
 * Time: 17:44
 */

namespace common\exceptions;

use yii\base\Exception;
use yii\base\Model;

class ModelSaveErrorException extends Exception
{
    public function __construct(Model $model, $code = 500)
    {
        parent::__construct(var_export($model->getErrors(), true), $code);
    }
}