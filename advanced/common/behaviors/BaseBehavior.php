<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/4
 * Time: 17:46
 */

namespace common\behaviors;


use common\controllers\PerformanceRecordController;
use yii\base\Behavior;

class BaseBehavior extends Behavior
{
    /*public function events()
    {
        $this->setPerformanceRecordAnchor(sprintf('获取事件前：%s', $this->owner->className()));
        $result = parent::events();
        $this->setPerformanceRecordAnchor(sprintf('获取事件后：%s', $this->owner->className()));

        return $result;
    }*/

    /*public function attach($owner)
    {
        $this->setPerformanceRecordAnchor(sprintf('附加行为事件前：%s', $owner->className()));
        $result = parent::attach($owner);
        $this->setPerformanceRecordAnchor(sprintf('附加行为事件后：%s', $owner->className()));

        return $result;
    }*/

    /*public function init()
    {
        $this->setPerformanceRecordAnchor(sprintf('执行行为事件前：%s', __METHOD__));
        $result = parent::init();
        $this->setPerformanceRecordAnchor(sprintf('执行行为事件后：%s', __METHOD__));

        return $result;
    }*/

    /**
     * @param $anchor
     */
    private function setPerformanceRecordAnchor($anchor)
    {
        PerformanceRecordController::$time_record[] = [$anchor, microtime(true)];
    }
}