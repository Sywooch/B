<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/13
 * Time: 19:25
 */

namespace common\components;


use common\helpers\TimeHelper;
use yii\base\Component;
use Yii;

class Monitor extends Component
{
    public static function checkCityWideCurfew()
    {
        $hour = date('G', TimeHelper::getCurrentTime());
        if ($hour < 10 || $hour > 20) {
            #Yii::$app->controller->redirect(['/default/index']);
        } else {
            Yii::$app->session->setFlash('danger', 'City Wide Curfew');

            return Yii::$app->controller->redirect(['/default/index'], 307);
        }
    }
}