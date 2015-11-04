<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/14
 * Time: 9:10
 */

namespace common\controllers;

use common\services\QuestionService;
use console\modules\crawler\services\CrawlerService;
use Yii;
use yii\helpers\Json;
use yii\web\Response;

class BaseController extends PerformanceRecordController
{
    /**
     * JSON输出
     * @param array $response ['code'=> '', 'msg' => '', 'data' => '']
     * @throws \yii\base\ExitException
     * @internal param $data
     */
    protected function jsonOut(array $response)
    {
        Yii::$app->getResponse()->clear();
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->setStatusCode(200);
        Yii::$app->response->data = $response;

        Yii::$app->end();
    }

    protected function htmlOut($html)
    {
        echo Json::encode($html);
        Yii::$app->end();
    }

    /**
     * 参数错误
     * @throws \yii\base\ExitException
     */
    protected function errorParam()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->error->error_param;
        Yii::$app->response->data = $data;
        Yii::$app->end();
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    /*protected function getQuestionService()
    {
        return Yii::createObject(QuestionService::className());
    }*/

    /*public function __call($action, $params)
    {
        var_dump($action, $params);
        exit('~~~~~');
        //parent::__call($action, $params);
    }*/
}