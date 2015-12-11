<?php
/**
 * Description:
 * License:
 * User: Keen
 * Date: 2015/10/11
 * Time: 22:42
 * Version:
 * Created by PhpStorm.
 */

namespace common\behaviors;

use Yii;
use yii\base\ErrorException;
use yii\behaviors\BlameableBehavior;

class OperatorBehavior extends BlameableBehavior
{
    protected function getValue($event)
    {
        if ($this->value === null) {
            $user = Yii::$app->get('user', false);

            if ($user) {
                return $user->id;
            } else {
                throw new ErrorException('当前动作需要登陆用户才可操作。');
            }
        } else {
            return call_user_func($this->value, $event);
        }
    }

    /*public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array)$this->attributes[$event->name];
            $value = $this->getValue($event);
            foreach ($attributes as $attribute) {
                // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
                //只更新没有值的属性
                if (is_string($attribute) && empty($this->owner->$attribute)) {
                    $this->owner->$attribute = $value;
                }
            }
        }
    }*/
}
