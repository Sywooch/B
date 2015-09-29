<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 15:43
 */

namespace backend\controllers;

use console\modules\crawler\controllers\crawlers\CrawlerBase;
use Yii;
use common\controllers\BaseController;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;


class TestController extends BaseController
{
    /*public function behaviors()
    {
        $behaviors = parent::behaviors();
        return ArrayHelper::merge($behaviors, [
                [
                        'class'    => 'yii\filters\HttpCache',
                        'only'     => ['index'],
                        //'cacheControlHeader' => 'public, max-age=3600',
                        'etagSeed' => function ($action, $params) {
                            $data = md5('abc');
                            return $data;
                        },
                ]
        ]);
    }*/

    public function actionIndex()
    {
        $crawler_sign = Inflector::camelize('sz_ben_di_bao');

        $crawler = Yii::createObject(
                [
                        'class'      => 'console\\modules\\crawler\\controllers\\crawlers\\' . $crawler_sign,
                        'id' => 1
                ]
        );

        print_r($crawler);

        if ($crawler instanceof CrawlerBase) {
            //$crawler->up();
            $crawler->launch();
            //$crawler->down();
        }

        /*sleep(0.5);
        $this->setPerformanceRecordAnchor('action index');
        sleep(0.5);*/
        /*$data = Yii::$app->error->common;
        print_r($data);*/
        //$this->errorParam();
        //echo Yii::$app->setting->get('test');
        #$this->render('index');
    }
}