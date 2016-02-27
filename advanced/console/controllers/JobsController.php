<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-02-27
 * Time: 12:01
 */

namespace console\controllers;

use shakura\yii2\gearman\JobWorkload;
use Yii;
use yii\helpers\ArrayHelper;

class JobsController extends BaseController
{
    public function actionIndex()
    {
        /*$result = Yii::$app->gearman->getDispatcher()->execute(
             'sync', new JobWorkload(
                       [
                           'params' => [
                               'data' => 'value',
                           ],
                       ]
                   )
         );*/

        $data = new JobWorkload(
            [
                'params' => [
                    'data' => 'value',
                ],
            ]
        );

        $function_names = array_keys(Yii::$app->gearman->jobs);

        for ($i = 0; $i < 1000; $i++) {
            foreach ($function_names as $function_name) {
                Yii::$app->gearman->getDispatcher()->background($function_name, $data);
            }
        }

        //print_r($result);
    }
}
