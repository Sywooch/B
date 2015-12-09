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
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class OperatorBehavior extends AttributeBehavior
{
    public $createdByAttribute = 'create_by';
    public $updatedByAttribute = 'modify_by';
    public $value;


    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_VALIDATE => $this->createdByAttribute,
                BaseActiveRecord::EVENT_BEFORE_UPDATE   => $this->updatedByAttribute,
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        $this->value = Yii::$app->user->id;

        return $this->value;
    }

    public function evaluateAttributes($event)
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
    }
}