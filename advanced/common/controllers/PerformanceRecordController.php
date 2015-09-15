<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/14
 * Time: 10:38
 */

namespace common\controllers;

use yii\web\Application;
use yii\web\Controller;

class PerformanceRecordController extends Controller
{
    private $time_record;
    private $action_name;
    private $start_time;
    const max_execute_time = 0.5;

    public function __construct($id, $module, $config = [])
    {
        $this->time_record = [];
        $this->start_time = microtime(true);
        parent::__construct($id, $module, $config);
        $this->time_record[] = ["After parent construct", microtime(true)];
    }

    public function beforeAction($action)
    {
        $this->action_name = $action->getUniqueId();
        $this->time_record[] = ["Before parent before action", microtime(true)];
        $result = parent::beforeAction($action);
        $this->time_record[] = ["After parent before action", microtime(true)];
        return $result;
    }

    public function afterAction($action, $result)
    {
        $this->time_record[] = ["Before parent after action", microtime(true)];
        $result = parent::afterAction($action, $result);
        $this->time_record[] = ["After parent after action", microtime(true)];
        \Yii::$app->on(Application::EVENT_AFTER_REQUEST, array($this, "appEnd"));
        return $result;
    }

    public function appEnd()
    {
        $this->time_record[] = ["APP END", microtime(true)];
        register_shutdown_function(array($this, "onShutdown"));
    }

    public function onShutdown()
    {
        $this->time_record[] = ["PHP shutdown", microtime(true)];

        $records = [];
        $previous_use_time = $this->start_time;

        foreach ($this->time_record as $item) {
            #到目前为止时间
            $used_time = $item[1] - $this->start_time;
            #该步执行时间
            $use_time = $item[1] - $previous_use_time;
            #判断该动作执行时否超时
            if ($use_time >= self::max_execute_time) {
                $records[] = sprintf("+++ [%2.3f/%2.3f] %s", $use_time, $used_time, $item[0]);
            } else {
                $records[] = sprintf("--- [%2.3f/%2.3f] %s", $use_time, $used_time, $item[0]);
            }

            $previous_use_time = $item[1];
        }

        $timeRec = PHP_EOL . "Action Name:" . $this->action_name . PHP_EOL . implode(PHP_EOL, $records);

        \Yii::trace($timeRec, "Performance");
    }

    /**
     * @param $anchor
     */
    protected function setPerformanceRecordAnchor($anchor)
    {
        $this->time_record[] = [$anchor, microtime(true)];
    }
}