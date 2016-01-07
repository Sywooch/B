<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/16
 * Time: 17:05
 */

namespace common\behaviors;

use ReflectionClass;
use ReflectionMethod;
use Yii;
use yii\helpers\Inflector;

/**
 * 只需要定义User::EVENT_QUESTION_CREATE　及　public function eventQuestionCreate()　的方法即可。
 * Class UserBehavior
 * @package common\behaviors
 * @property \common\components\user\User owner
 */
class UserEventBehavior extends BaseBehavior
{
    /**
     * 自动获取本类下面所有的public方法
     * @return array
     */
    private function getAllEvents()
    {
        $class = new ReflectionClass(get_called_class());
        $all_method = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        $methods = [];
        foreach ($all_method as $method) {
            if ($method->name != 'events') {
                $methods[Inflector::underscore($method->name)] = $method->name;
            }
        }

        return $methods;
    }

    public function events()
    {
        $events = $this->getAllEvents();

        return $events;
    }

    public function eventQuestionCreate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');
        $this->dealWithFeed(__FUNCTION__);
        $this->dealWithCredit(__FUNCTION__);
        $this->dealWithGrade(__FUNCTION__);
    }

    private function dealWithCredit($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');
        $event_name = Inflector::underscore($event);

        return $this->owner->executeCreditRule($event_name);
    }

    private function dealWithGrade($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');
    }

    private function dealWithFeed($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');
    }
}
