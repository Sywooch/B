<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/2
 * Time: 18:32
 */

namespace common\helpers;


class ServerHelper
{
    public static function checkIsSpider()
    {
        $spider = implode(
            '|',
            ["baiduspider", "googlebot", "sosospider", "360spider", "yodaobot", "sogou", "msnbot", "bingbot"]
        );

        if (preg_match("/($spider)/i", $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }
}