<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-02-27
 * Time: 11:26
 */

namespace console\jobs;

use GearmanJob;
use shakura\yii2\gearman\JobBase;
use Yii;
use yii\helpers\Json;

class Sync extends JobBase
{
    public function execute(GearmanJob $job = null)
    {
        // Do something

        $work_load = $this->getWorkload($job);

        //print_r($work_load);

        return true;
    }
}
