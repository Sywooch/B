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


/*应用级缓存*/
#USER
const REDIS_KEY_USER = 'user';
const REDIS_KEY_USER_USERNAME_ID = 'username_id';
const REDIS_KEY_USER_FOLLOW = 'user_follow';
const REDIS_KEY_USER_TAG_RELATION = 'user_tag_relation';

#TAG
const REDIS_KEY_TAG_ID_NAME = 'tag_id_name';
const REDIS_KEY_TAG_NAME_ID = 'tag_name_id';
const REDIS_KEY_TAG_USER_RELATION = 'tag_user_relation';


#QUESTION
const REDIS_KEY_QUESTION = 'question';

/*系统级缓存*/
const REDIS_KEY_SETTING = 'setting';
const REDIS_KEY_COUNTER = 'counter';
const REDIS_KEY_COUNTER_SET = 'counter_set';
const REDIS_KEY_NOTIFIER = 'notifier';
const REDIS_KEY_NOTIFIER_SET = 'notifier_set';
const REDIS_KEY_UPDATER = 'updater';
const REDIS_KEY_UPDATER_SET = 'updater_set';

const REDIS_KEY_EMAIL = 'email';

/**
 * 注意，返回的 key　不得为数字，必须为字符串, expire = 0 表示永久存在
 */
return [
    /************************************************/
    #应用设置
    REDIS_KEY_SETTING           => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #打点器队列
    REDIS_KEY_COUNTER           => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    REDIS_KEY_COUNTER_SET       => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #通知器队列
    REDIS_KEY_NOTIFIER          => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    REDIS_KEY_NOTIFIER_SET      => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #更新器队列
    REDIS_KEY_UPDATER           => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    REDIS_KEY_UPDATER_SET       => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #注册邮件激活
    REDIS_KEY_EMAIL             => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    /***************************************************/
    #用户数据
    REDIS_KEY_USER              => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #用户名与用户ID间的关系
    REDIS_KEY_USER_USERNAME_ID  => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #用户关注的用户
    REDIS_KEY_USER_FOLLOW       => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #用户关注的tag
    REDIS_KEY_USER_TAG_RELATION => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    /*************************************************/
    #tag id & name
    REDIS_KEY_TAG_ID_NAME       => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #tag name & id
    REDIS_KEY_TAG_NAME_ID       => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #tag has user top 10
    REDIS_KEY_TAG_USER_RELATION => [
        'server' => $servers['master'],
        'expire' => 86400 * 2, #有效期
    ],
    #question
    REDIS_KEY_QUESTION          => [
        'server' => $servers['master'],
        'expire' => 86400 * 2, #有效期
    ],
    /*************************************************************/
    #临时测试类的缓存
    'abcd'                      => [
        'server' => $servers['master'],
        'expire' => 500,
    ],
    's'                         => [
        'server' => $servers['master'],
        'expire' => 500,
    ],
    'm'                         => [
        'server' => $servers['master'],
        'expire' => 500,
    ],
    'master'                    => [
        'server' => $servers['master'],
        'expire' => 500,
    ],
    'slave'                     => [
        'server' => $servers['slave'],
        'expire' => 500,
    ],
];