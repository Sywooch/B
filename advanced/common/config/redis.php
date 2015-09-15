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
    ]
];

return [
    #应用设置
    'setting' => [
            'server' => $servers['master'],
            'key'    => 'app_setting',
            'expire' => 8640000,
    ],
    #临时测试类的缓存
    'abc'     => [
            'server' => $servers['master'],
            'key'    => 'test_abc',
            'expire' => 5,
    ],
];