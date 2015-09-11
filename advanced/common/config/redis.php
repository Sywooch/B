<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 19:18
 */

$servers = [
        'master' => [
                'hostname' => '127.0.0.1',
                'port'     => 6379,
                'database' => 0,
                'auth' => '',
        ]
];

return [
        'setting' => [
                'server' => $servers['master'],
                'key'    => 'app_setting',
                'expire' => 86400,
        ],

        'abc' => [
                'server' => $servers['master'],
                'key'    => 'test_abc',
                'expire' => 5,
        ],
];