<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/14
 * Time: 9:16
 */

namespace common\components\error;

use yii\helpers\Inflector;

class ErrorResponse
{
    public $config;


    /**
     * 根据错误ID，返回错误信息
     * @param $error_id
     * @return array
     */
    public function get($error_id)
    {
        if (isset($this->config[$error_id])) {
            list($code, $msg) = $this->config[$error_id];
        } else {
            $code = null;
            $msg = "错误代码：{$error_id}未定义。";
        }

        return [
                'code' => $code,
                'msg'  => $msg,
        ];
    }

    /**
     * 通过 Yii::$app->error->xxx()调用
     * @param $action
     * @param $params
     * @return array
     */
    public function __call($action, $params)
    {
        $error_id = strtoupper(str_replace(' ', '_', Inflector::camel2words($action, false)));

        return $this->get($error_id);
    }

    /**
     * 通过 Yii::$app->error->xxx 调用
     * @param $action
     * @return array
     */
    public function __get($action)
    {
        $error_id = strtoupper(str_replace(' ', '_', Inflector::camel2words($action, false)));

        return $this->get($error_id);
    }
}