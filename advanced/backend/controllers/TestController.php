<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 15:43
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;


class TestController extends Controller
{
    public function actionIndex()
    {
        echo Yii::$app->setting->get('test');

    }
}