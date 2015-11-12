<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2015/10/18
 * Time: 0:21
 */

namespace common\helpers;


class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * 字符串长度，不区别中英文，一个汉字长度为1
     * @param $str
     * @return int
     */
    public static function countStringLength($str)
    {
        if (empty($str)) {
            return 0;
        }
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'utf-8');
        } else {
            preg_match_all("/./u", $str, $ar);

            return count($ar[0]);
        }
    }

    public static function checkEmailFormat($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}