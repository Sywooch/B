<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/14
 * Time: 9:10
 */

namespace console\controllers;

use console\modules\crawler\services\CrawlerService;
use Yii;
use yii\console\Controller;

class BaseController extends Controller
{


    /**
     * 获取Crawler服务
     * @return \console\modules\crawler\services\CrawlerService object
     * @throws \yii\base\InvalidConfigException
     */
    public function getCrawlerService()
    {
        return Yii::createObject(CrawlerService::className());
    }
}