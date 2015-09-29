<?php
/**
 * Description:
 * License:
 * User: Keen
 * Date: 2015/9/27
 * Time: 1:44
 * Version:
 * Created by PhpStorm.
 */

namespace console\modules\crawler\services;

use console\modules\crawler\models\Crawler;
use yii\base\Object;
use yii\helpers\Inflector;

class CrawlerService extends Object
{
    public $crawler;

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * 获取指定的爬虫器
     * @param $id
     * @return array|Crawler|null
     */
    public function getCrawler($id)
    {
        $crawler = new Crawler();
        $result = $crawler::find()->where(
            [
                'id' => $id,
                'status' => 'Y'
            ]
        )->one();

        return $result;
    }

    /**
     * 获取所有有效的爬虫器
     * @return array
     */
    public function getAllCrawlersData()
    {
        $crawler = new Crawler();
        $data = $crawler::find()->where(
            ['status' => 'Y']
        )->andWhere(['or',
            'next_execute_time=0',
            'next_execute_time<=' . time()
        ])->asArray()->all();

        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'sign' => Inflector::camelize($item['sign']),
                'next_execute_time' => $item['next_execute_time'],
                'last_execute_time' => $item['last_execute_time'],
            ];
        }

        return $result;
    }
}