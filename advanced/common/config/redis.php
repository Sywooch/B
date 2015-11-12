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
const REDIS_KEY_USER = 'user:hash';
const REDIS_KEY_USER_USERNAME_USERID = 'username_userid:string';

const REDIS_KEY_USER_FOLLOW = 'user_follow:string';
const REDIS_KEY_USER_TAG_RELATION = 'user_tag_relation:string';

#TAG
const REDIS_KEY_TAG = 'tag:hash';
const REDIS_KEY_TAG_ID_NAME = 'tag_id_name:string';
const REDIS_KEY_TAG_NAME_ID = 'tag_name_id:string';
const REDIS_KEY_TAG_USER_RELATION = 'tag_user_relation:string';


#QUESTION
const REDIS_KEY_QUESTION = 'question:hash';
const REDIS_KEY_QUESTION_BLOCK = 'question_block:string';
const REDIS_KEY_QUESTION_HAS_ANSWERED = 'question_has_answered:string';
#ANSWER
const REDIS_KEY_ANSWER_LIST = 'answer_list:string';
const REDIS_KEY_ANSWER_LIST_TIME = 'answer_list_time:sset';
const REDIS_KEY_ANSWER_LIST_SCORE = 'answer_list_score:sset';
const REDIS_KEY_ANSWER_ENTITY = 'answer_entity:list';

/*系统级缓存*/
const REDIS_KEY_SESSION = 'session:string';

const REDIS_KEY_SETTING = 'setting:string';

const REDIS_KEY_COUNTER = 'counter:list';
const REDIS_KEY_COUNTER_SET = 'counter:set';
const REDIS_KEY_NOTIFIER = 'notifier:list';
const REDIS_KEY_NOTIFIER_SET = 'notifier:set';
const REDIS_KEY_UPDATER = 'updater:list';
const REDIS_KEY_UPDATER_SET = 'updater:set';

const REDIS_KEY_EMAIL = 'email:list';
const REDIS_KEY_RBAC = 'rbac:string';

/**
 * 注意，返回的 key　不得为数字，必须为字符串, expire = 0 表示永久存在
 */
return [
    /************************************************/
    #SESSION设置
    REDIS_KEY_SESSION               => [
        'server' => $servers['master'],
        'expire' => 86400,
    ],#应用设置
    REDIS_KEY_SETTING               => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #打点器队列
    REDIS_KEY_COUNTER               => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    REDIS_KEY_COUNTER_SET           => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #通知器队列
    REDIS_KEY_NOTIFIER              => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    REDIS_KEY_NOTIFIER_SET          => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #更新器队列
    REDIS_KEY_UPDATER               => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    REDIS_KEY_UPDATER_SET           => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #注册邮件激活
    REDIS_KEY_EMAIL                 => [
        'server' => $servers['master'],
        'expire' => 0,
    ],
    #RBAC权限
    REDIS_KEY_RBAC                  => [
        'server' => $servers['master'],
        'expire' => 86400 * 7,
    ],
    /***************************************************/
    #用户数据
    REDIS_KEY_USER                  => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #用户名与用户ID间的关系
    REDIS_KEY_USER_USERNAME_USERID  => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #用户关注的用户
    REDIS_KEY_USER_FOLLOW           => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #用户关注的tag
    REDIS_KEY_USER_TAG_RELATION     => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    /*************************************************/
    #tag
    REDIS_KEY_TAG                   => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #tag id & name
    REDIS_KEY_TAG_ID_NAME           => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #tag name & id
    REDIS_KEY_TAG_NAME_ID           => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    #tag has user top 10
    REDIS_KEY_TAG_USER_RELATION     => [
        'server' => $servers['master'],
        'expire' => 86400 * 2, #有效期
    ],
    #question
    REDIS_KEY_QUESTION              => [
        'server' => $servers['master'],
        'expire' => 86400, #有效期
    ],
    REDIS_KEY_QUESTION_BLOCK        => [
        'server' => $servers['master'],
        'expire' => 86400, #有效期
    ],
    REDIS_KEY_QUESTION_HAS_ANSWERED => [
        'server' => $servers['master'],
        'expire' => 86400 * 7, #有效期
    ],
    /*------------- answer ---------------*/
    REDIS_KEY_ANSWER_LIST           => [
        'server' => $servers['master'],
        'expire' => 86400, #有效期
    ],
    REDIS_KEY_ANSWER_LIST_TIME      => [
        'server' => $servers['master'],
        'expire' => 86400, #有效期
    ],
    REDIS_KEY_ANSWER_LIST_SCORE     => [
        'server' => $servers['master'],
        'expire' => 86400, #有效期
    ],
    REDIS_KEY_ANSWER_ENTITY         => [
        'server' => $servers['master'],
        'expire' => 86400, #有效期
    ],
    /*************************************************************/
    #临时测试类的缓存
    'abcd'                          => [
        'server' => $servers['master'],
        'expire' => 500,
    ],
    's'                             => [
        'server' => $servers['master'],
        'expire' => 500,
    ],
    'm'                             => [
        'server' => $servers['master'],
        'expire' => 500,
    ],
    'master'                        => [
        'server' => $servers['master'],
        'expire' => 500,
    ],
    'slave'                         => [
        'server' => $servers['slave'],
        'expire' => 500,
    ],
];