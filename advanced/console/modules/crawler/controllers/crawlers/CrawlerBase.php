<?php
namespace console\modules\crawler\controllers\crawlers;

use console\controllers\BaseController;
use console\modules\crawler\models\Crawler;
use Yii;
use yii\base\Component;
use yii\base\Object;

/**
 * 爬虫器基类
 * Crawler controller
 */
class CrawlerBase extends Object
{
    public $id;
    public $instance;

    /**
     * 初始化
     */
    public function init()
    {
        $this->instance = Crawler::find()->where('id=:id', [':id' => $this->id])->one();
    }

    public function up()
    {

    }

    public function down()
    {

    }
}
