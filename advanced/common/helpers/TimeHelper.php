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
     * @param bool $nature
     * @return int
     */
    public static function getCurrentTime($nature = false)
    {
        if ($nature) {
            return mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        } else {
            return time();
        }

    }
    
    /**
     * 获取距X天前最迟的时间
     * @param int  $period
     * @param bool $nature true:自然天，即X天前（从零点开始），false：非自然天，即从当前时间往前X天
     * @return int
     */
    public static function getBeforeTime($period = 7, $nature = false)
    {
        return self::getCurrentTime($nature) - $period * 86400;
    }
    
    public static function getAfterTime($period = 7, $nature = false)
    {
        return self::getCurrentTime($nature) + $period * 86400;
    }
    
    public static function getThisYearStartTime()
    {
        return mktime(0, 0, 0, 0, 0, date('Y'));
    }
    
    public static function getThisYearEndTime()
    {
        return mktime(23, 59, 59, 12, 31, date('Y'));
    }
    
    public static function getThisSeasonStartTime()
    {
        //当月是第几季度
        $season = ceil((date('n')) / 3);

        return mktime(0, 0, 0, $season * 3 - 3 + 1, 1, date('Y'));
    }

    public static function getThisSeasonEndTime()
    {
        //当月是第几季度
        $season = ceil((date('n')) / 3);

        return mktime(23, 59, 59, $season * 3, date('t', mktime(0, 0, 0, $season * 3, 1, date("Y"))), date('Y'));
    }

    public static function getThisMonthStartTime()
    {
        return mktime(0, 0, 0, date("m"), 1, date("Y"));
    }

    public static function getThisMonthEndTime()
    {
        return mktime(23, 59, 59, date("m"), date("t"), date("Y"));
    }

    public static function getThisWeekStartTime()
    {
        return mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
    }

    public static function getThisWeekEndTime()
    {
        return mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));
    }

    public static function getTodayStartTime()
    {
        return mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    }

    public static function getTodayEndTime()
    {
        return mktime(23, 59, 59, date("m"), date("d"), date("Y"));
    }
}