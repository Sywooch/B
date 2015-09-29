<?php
/**
 * Description:
 * License:
 * User: Keen
 * Date: 2015/9/27
 * Time: 2:32
 * Version:
 * Created by PhpStorm.
 */

namespace console\modules\crawler\controllers\crawlers;

use console\modules\crawler\interfaces\CrawlerInterface;
use Yii;

class SzBenDiBao extends CrawlerBase implements CrawlerInterface
{

    public $crawler_id;


    public function launch()
    {
        var_dump($this->crawler_id);
    }
}