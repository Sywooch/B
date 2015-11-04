<?php
/**
 * Description: 填充IP地址
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


class IpBehavior extends AttributeBehavior
{
    public $ipAttribute = 'ip';
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
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->ipAttribute,
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        $this->value = Yii::$app->request->userIP ? ip2long(Yii::$app->request->userIP) : null;

        return $this->value;
    }
}