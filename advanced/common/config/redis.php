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
    ],
];

const REDIS_KEY_USER = 'user';

/**
 * 注意，返回的 key　不得为数字，必须为字符串, expire = 0 表示永久存在
 */
return [
    /************************************************/
    #应用设置
    'setting'      => [
        'server' => $servers['master'],
        'key'    => 'app_setting',
        'expire' => 8640000,
    ],
    #打点器队列
    'counter'      => [
        'server' => $servers['master'],
        'key'    => 'counter_queue',
        'expire' => 0,
    ],
    'counter_set'  => [
        'server' => $servers['master'],
        'key'    => 'counter_set',
        'expire' => 0,
    ],
    #通知器队列
    'notifier'     => [
        'server' => $servers['master'],
        'key'    => 'notifier_queue',
        'expire' => 0,
    ],
    'notifier_set' => [
        'server' => $servers['master'],
        'key'    => 'notifier_set',
        'expire' => 0,
    ],
    #更新器队列
    'updater'      => [
        'server' => $servers['master'],
        'key'    => 'updater_queue',
        'expire' => 0,
    ],
    'updater_set'  => [
        'server' => $servers['master'],
        'key'    => 'updater_set',
        'expire' => 0,
    ],

    /***************************************************/
    #用户数据
    'user'         => [
        'server' => $servers['master'],
        'key'    => 'user',
        'expire' => 43200, #半天有效期
    ],
    #用户名与用户ID间的关系
    'username'         => [
        'server' => $servers['master'],
        'key'    => 'username',
        'expire' => 43200, #半天有效期
    ],
    /*************************************************************/
    #临时测试类的缓存
    'abcd'         => [
        'server' => $servers['master'],
        'key'    => 'aa',
        'expire' => 500,
    ],
    's'         => [
        'server' => $servers['master'],
        'key'    => 's',
        'expire' => 500,
    ],
    'm'         => [
        'server' => $servers['master'],
        'key'    => 'm',
        'expire' => 500,
    ],
    'master'       => [
        'server' => $servers['master'],
        'key'    => 'test_master',
        'expire' => 500,
    ],
    'slave'        => [
        'server' => $servers['slave'],
        'key'    => 'test_slave',
        'expire' => 500,
    ],
];