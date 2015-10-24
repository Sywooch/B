<?php

namespace console\modules\crawler\controllers;

use console\controllers\BaseController;
use console\modules\crawler\controllers\crawlers\CrawlerBase;
use console\modules\crawler\controllers\crawlers\SzBenDiBao;
use Yii;

class DefaultController extends BaseController
{

    public function actionIndex()
    {
        $crawlers = $this->getAllCrawlers();

        foreach ($crawlers as $crawler) {
            $this->runCrawl($crawler['id'], $crawler['sign']);
        }
    }


    /**
     * 获取所有爬虫器
     */
    public function getAllCrawlers()
    {
        return $this->getCrawlerService()->getAllCrawlersData();
    }

    private function runCrawl($crawler_id, $crawler_sign)
    {
        $crawler = Yii::createObject(
                [
                        'class' => 'console\\modules\\crawler\\controllers\\crawlers\\' . $crawler_sign,
                        'id'    => $crawler_id
                ]
        );
        if ($crawler instanceof CrawlerBase) {
            $crawler->up();
            $crawler->launch();
            $crawler->down();
        }
    }
}
