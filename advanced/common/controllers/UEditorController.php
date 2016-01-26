<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-01-26
 * Time: 11:49
 */

namespace common\controllers;

use Yii;
use crazydb\ueditor\UEditorController as BaseUEditorController;

class UEditorController extends BaseUEditorController
{
    public function init()
    {
        parent::init();
        //do something
    }

    public function actionConfig()
    {
        //do something
        //这里可以对 config 请求进行自定义响应
    }

}
