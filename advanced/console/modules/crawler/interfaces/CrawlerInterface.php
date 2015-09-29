<?php
/**
 * Description:
 * License:
 * User: Keen
 * Date: 2015/9/27
 * Time: 0:54
 * Version:
 * Created by PhpStorm.
 */

namespace console\modules\crawler\interfaces;


interface CrawlerInterface
{
    public function up();

    public function down();

    public function launch();
}