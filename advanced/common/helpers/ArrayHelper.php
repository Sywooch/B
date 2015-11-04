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
    /**
     * 比 ArrayHelper::isAssociative还要严格的关联数组，
     * @param $array
     * @return bool
     */
    public static function isPureAssociative($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}