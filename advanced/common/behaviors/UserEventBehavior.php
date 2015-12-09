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
 * 只需要定义User::EVENT_USER_CREATE_QUESTION　及　public function eventUserCreateQuestion()　的方法即可。
 * Class UserBehavior
 * @package common\behaviors
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
        Yii::trace('Begin ' . $this->className(), 'user_event');
        $events = $this->getAllEvents();

        return $events;
    }

    public function eventUserCreateQuestion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'user_event');
        //todo
    }


}