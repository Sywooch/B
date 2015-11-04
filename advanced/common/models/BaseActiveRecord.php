<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/29
 * Time: 15:45
 */

namespace common\models;


use yii\db\ActiveRecord;
use Yii;

class BaseActiveRecord extends ActiveRecord
{
    public function afterValidate()
    {
        if (parent::afterValidate()) {
            if ($this->getErrors()) {
                Yii::error($this->getErrors(), implode('-', ['BaseActiveRecord', $this->tableName()]));
            }
        }
    }
}