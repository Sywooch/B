<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/23
 * Time: 18:27
 */

namespace common\helpers;


class ArrayHelper extends \yii\helpers\ArrayHelper
{
    public function isPureAssociative($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}