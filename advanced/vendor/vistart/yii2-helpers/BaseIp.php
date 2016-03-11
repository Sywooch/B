<?php

/*
 *   _   __ __ _____ _____ ___  ____  _____
 *  | | / // // ___//_  _//   ||  __||_   _|
 *  | |/ // /(__  )  / / / /| || |     | |
 *  |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Helpers;

/**
 * Description of BaseIp
 *
 * @author vistart
 */
class BaseIp 
{
    public static function IPv4toInteger($ip)
    {
        $ips = explode('.', $ip);
        $integer = 0;
        for ($i = 0; $i < 4; $i++)
        {
            $ips[$i] = intval($ips[$i]) % 256;
            $ips[$i] = pow(256, 3 - $i) * intval($ips[$i]);
            $integer += $ips[$i];
        }
        return $integer;
    }
    
    public static function ip2long($ip)
    {
        return trim(sprintf("%u", ip2long($ip)));
    }
    
    public static function long2ip($long)
    {
        return long2ip($long);
    }
    
    public static function IPv6toLong($ip)
    {
        $ip_n = inet_pton($ip);
        $bits = 15; // 16 x 8 bit = 128 bit (ipv6)
        $ipbin = '';
        while ($bits >= 0)
        {
            $bin = sprintf("%08b", (ord($ip_n[$bits])));
            $ipbin = $bin.$ipbin;
            $bits--;
        }
        return $ipbin;
    }
    
    public static function LongtoIPv6($bin)
    {
        $pad = 128 - strlen($bin);
        for ($i = 1; $i <= $pad; $i++)
        {
            $bin = "0".$bin;
        }
        $bits = 0;
        $ipv6 = '';
        while ($bits <= 7)
        {
            $bin_part = substr($bin, ($bits * 16), 16);
            $ipv6 .= dechex(bindec(($bin_part))) . ":";
            $bits++;
        }
        return inet_ntop(inet_pton(substr($ipv6, 0, -1)));
    }
    
    public static function splitIPv6($ipbin)
    {
        $ips[] = (substr($ipbin, 0, 32));
        $ips[] = (substr($ipbin, 32, 32));
        $ips[] = (substr($ipbin, 64, 32));
        $ips[] = (substr($ipbin, 96, 32));
        return $ips;
    }
    
    public static function populateIPv6($ips)
    {
        $ipbin  = sprintf('%032b', bindec($ips[0]));
        $ipbin .= sprintf('%032b', bindec($ips[1]));
        $ipbin .= sprintf('%032b', bindec($ips[2]));
        $ipbin .= sprintf('%032b', bindec($ips[3]));
        return $ipbin;
    }
    
    const IPv4 = 4;
    const IPv6 = 6;
    
    public static function judgeIPtype($ip)
    {
        if (strpos($ip, ':') === false){
            return self::IPv4;
        }
        return self::IPv6;
    }
}
