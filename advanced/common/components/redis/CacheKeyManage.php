<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 18:40
 */

namespace common\components\redis;


class CacheKeyManage
{
    const DECOLLATOR = ':';


    #վ�����û���
    const SETTING = 'app_setting';

    public static function setting($code)
    {
        return self::build('setting', $code);
    }


    public static function build($prefix, $sign)
    {
        return $prefix . self::DECOLLATOR . $sign;
    }

}