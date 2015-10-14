<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/12
 * Time: 10:07
 */

namespace common\behaviors;

use Yii;
use yii\base\InvalidCallException;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class TimestampBehavior extends AttributeBehavior
{
    public $createdAtAttribute = 'create_at';
    public $updatedAtAttribute = 'modify_at';
    public $value;


    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::trace('Begin '. $this->className(), 'behavior');

        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => $this->createdAtAttribute,
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        $this->value = time();

        Yii::trace('GetValue '. $this->value, 'behavior');

        return $this->value;
    }


    public function touch($attribute)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Updating the timestamp is not possible on a new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }
}
