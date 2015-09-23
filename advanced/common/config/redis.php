<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 19:18
 */

$servers = [
    #主服务器
    'master' => [
            'hostname' => '127.0.0.1',
            'port'     => 6379,
            'database' => 0,
            'auth'     => '',
    ],
    'slave'  => [
            'hostname' => '127.0.0.1',
            'port'     => 6380,
            'database' => 0,
            'auth'     => '',
    ]
];

/**
 * 注意，返回的 key　不得为数字，必须为字符串
 */
return [
    #应用设置
    'setting' => [
            'server' => $servers['master'],
            'key'    => 'app_setting',
            'expire' => 8640000,
    ],
    #临时测试类的缓存
    'abcd'    => [
            'server' => $servers['master'],
            'key'    => 'aa',
            'expire' => 500,
    ],
    'master'    => [
            'server' => $servers['master'],
            'key'    => 'test_master',
            'expire' => 500,
    ],
    'slave'    => [
            'server' => $servers['slave'],
            'key'    => 'test_slave',
            'expire' => 500,
    ],
];