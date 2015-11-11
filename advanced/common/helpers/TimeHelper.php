<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/11
 * Time: 21:04
 */

namespace common\helpers;


class TimeHelper
{
    /**
     * 获取当前时间
     * @return int
     */
    public static function getCurrentTime()
    {
        return time();
    }

    /**
     * 获取距X天前最迟的时间
     * @param int $period
     * @return int
     */
    public static function getBeforeTime($period = 7)
    {
        return self::getCurrentTime() - $period * 86400;
    }

    public static function getAfterTime($period = 7)
    {
        return self::getCurrentTime() + $period * 86400;
    }
}