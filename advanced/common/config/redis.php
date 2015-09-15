<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 19:18
 */

$servers = [
    #��������
    'master' => [
            'hostname' => '127.0.0.1',
            'port'     => 6379,
            'database' => 0,
            'auth'     => '',
    ]
];

return [
    #Ӧ������
    'setting' => [
            'server' => $servers['master'],
            'key'    => 'app_setting',
            'expire' => 8640000,
    ],
    #��ʱ������Ļ���
    'abc'     => [
            'server' => $servers['master'],
            'key'    => 'test_abc',
            'expire' => 5,
    ],
];