<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 14-9-18
 * Time: 下午3:54
 * To change this template use File | Settings | File Templates.
 */

namespace app\controllers;

use yii\rest\ActiveController;


class TestController extends ActiveController
{
    public $modelClass = 'app\models\Test';
}