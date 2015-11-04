<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/14
 * Time: 10:38
 */

namespace common\controllers;

use Yii;
use yii\web\Application;
use yii\web\Controller;

class PerformanceRecordController extends Controller
{
    public static $time_record;
    private $action_name;
    private $start_time;
    const MAX_EXECUTE_TIME = 0.2;
    const MIN_EXECUTE_TIME = 0.1;

    public function __construct($id, $module, $config = [])
    {
        self::$time_record = [];
        $this->start_time = microtime(true);
        parent::__construct($id, $module, $config);
        self::$time_record[] = ["After parent construct", microtime(true)];
    }
    
    public function beforeAction($action)
    {
        $this->action_name = $action->getUniqueId();
        self::$time_record[] = ["Before parent before action", microtime(true)];
        $result = parent::beforeAction($action);
        self::$time_record[] = ["After parent before action", microtime(true)];
        
        return $result;
    }
    
    public function afterAction($action, $result)
    {
        self::$time_record[] = ["Before parent after action", microtime(true)];
        $result = parent::afterAction($action, $result);
        self::$time_record[] = ["After parent after action", microtime(true)];
        Yii::$app->on(Application::EVENT_AFTER_REQUEST, [$this, "appEnd"]);
        
        return $result;
    }
    
    public function appEnd()
    {
        self::$time_record[] = ["APP END", microtime(true)];
        register_shutdown_function([$this, "onShutdown"]);
    }
    
    public function onShutdown()
    {
        self::$time_record[] = ["PHP shutdown", microtime(true)];
        
        $records = [];
        $previous_use_time = $this->start_time;
        
        foreach (self::$time_record as $item) {
            #到目前为止时间
            $used_time = $item[1] - $this->start_time;
            #该步执行时间
            $use_time = $item[1] - $previous_use_time;
            #判断该动作执行时否超时
            if ($use_time >= self::MAX_EXECUTE_TIME) {
                $sign = '+++';
            } elseif ($use_time >= self::MIN_EXECUTE_TIME) {
                $sign = '---';
            } else {
                $sign = '   ';
            }

            $records[] = sprintf("%s [%2.3f/%2.3f] %s", $sign, $use_time, $used_time, $item[0]);

            $previous_use_time = $item[1];
        }
        
        $timeRec = sprintf(
            '%s%s%s%s%s%s%s%s',
            PHP_EOL,
            str_repeat('-', 30),
            PHP_EOL,
            "Action Name:" . $this->action_name,
            PHP_EOL,
            str_repeat('-', 30),
            PHP_EOL,
            implode(
                PHP_EOL,
                $records
            )
        );
        
        Yii::info($timeRec, "Performance");
    }
    
    /**
     * @param $anchor
     */
    protected function setPerformanceRecordAnchor($anchor)
    {
        self::$time_record[] = [$anchor, microtime(true)];
    }
}