<?php
/**
 * todo 上线后，需要移除trigger的监控方法
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/29
 * Time: 15:45
 */

namespace common\models;


use common\controllers\PerformanceRecordController;
use yii\base\Event;
use yii\db\ActiveRecord;
use Yii;

class BaseActiveRecord extends ActiveRecord
{
    
    /*public function __construct($config = [])
    {
        $this->setPerformanceRecordAnchor(sprintf('实例化前：%s', self::className()));
        parent::__construct($config);
        $this->setPerformanceRecordAnchor(sprintf('实例化后：%s', self::className()));
    }*/
    
    /*public function __call($action, $params)
    {
        $this->setPerformanceRecordAnchor(sprintf('调用前：%s::%s', self::className(), $action));
        $result = parent::__call($action, $params);
        $this->setPerformanceRecordAnchor(sprintf('调用前：%s::%s', self::className(), $action));

        return $result;
    }*/
    
    public function beforeValidate()
    {
        $this->setPerformanceRecordAnchor(
            sprintf('开始准备数据验证：%s::%s', self::className(), $this->getCurrentMethod(__METHOD__))
        );
        $result = parent::beforeValidate();
        $this->setPerformanceRecordAnchor(
            sprintf('完成准备数据验证：%s::%s', self::className(), $this->getCurrentMethod(__METHOD__))
        );
        
        return $result;
    }
    
    public function afterValidate()
    {
        $this->setPerformanceRecordAnchor(
            sprintf('开始完成数据验证：%s::%s', self::className(), $this->getCurrentMethod(__METHOD__))
        );
        if (parent::afterValidate()) {
            if ($this->getErrors()) {
                Yii::error($this->getErrors(), implode('-', ['BaseActiveRecord', $this->tableName()]));
            }
        }
        $this->setPerformanceRecordAnchor(
            sprintf('结束完成数据验证：%s::%s', self::className(), $this->getCurrentMethod(__METHOD__))
        );
    }
    
    public function beforeSave($insert)
    {
        $this->setPerformanceRecordAnchor(
            sprintf('开始准备保存：%s::%s', self::className(), $this->getCurrentMethod(__METHOD__))
        );
        $result = parent::beforeSave($insert);
        $this->setPerformanceRecordAnchor(
            sprintf('完成准备保存：%s::%s', self::className(), $this->getCurrentMethod(__METHOD__))
        );
        
        return $result;
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        $this->setPerformanceRecordAnchor(
            sprintf('开始保存后触发：%s::%s', self::className(), $this->getCurrentMethod(__METHOD__))
        );
        $result = parent::afterSave($insert, $changedAttributes);
        $this->setPerformanceRecordAnchor(
            sprintf('完成保存后触发：%s::%s', self::className(), $this->getCurrentMethod(__METHOD__))
        );
        
        return $result;
    }
    
    public function trigger($name, Event $event = null)
    {
        $this->setPerformanceRecordAnchor(sprintf('触发前：%s::%s', self::className(), $name));
        parent::trigger($name, $event);
        $this->setPerformanceRecordAnchor(sprintf('触发后：%s::%s', self::className(), $name));
    }
    
    
    private function setPerformanceRecordAnchor($anchor)
    {
        PerformanceRecordController::$time_record[] = [$anchor, microtime(true)];
    }
    
    private function getCurrentMethod($method)
    {
        $methods = explode('::', $method);
        
        if (count($methods) == 1) {
            return $methods[0];
        } else {
            return $methods[1];
        }
    }

    /**
     * 获取当前时间
     * @return int
     */
    protected function getCurrentTime()
    {
        return time();
    }

    /**
     * 获取距X天前最迟的时间
     * @param int $period
     * @return int
     */
    public function getBeforeTime($period = 7)
    {
        return $this->getCurrentTime() - $period * 86400;
    }

    public function getAfterTime($period = 7)
    {
        return $this->getCurrentTime() + $period * 86400;
    }
}