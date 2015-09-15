<?php
/**
 * 错误代码定义文件 'COMMON' => ['code' => 1, 'msg' => '常规错误']
 * 控制器获取方式　Yii::$app->error->common()
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/14
 * Time: 9:45
 */

return [
    #系统级错误　1000~9999
    'COMMON'      => [1000, '常规错误'],
    'ERROR_PARAM' => [1001, '参数不正确'],
    #后台错误 10000~99999


    #前台错误 100000~999999
];