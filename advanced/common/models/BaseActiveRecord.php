<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/29
 * Time: 15:45
 */

namespace common\models;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class BaseActiveRecord extends ActiveRecord
{
    /*public function behaviors()
    {
        return [
                [
                        'class'              => TimestampBehavior::className(),
                        'createdAtAttribute' => 'create_at',
                        'updatedAtAttribute' => 'modify_at',
                        'value'              => new Expression('NOW()'),
                ]
        ];
    }*/
}