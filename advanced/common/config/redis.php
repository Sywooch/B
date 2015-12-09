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

const REDIS_KEY_USER_FRIENDS = 'user_friends:set';
const REDIS_KEY_USER_FANS = 'user_fans:set';

const REDIS_KEY_USER_TAG_RELATION = 'user_tag_relation:sset';
const REDIS_KEY_USER_TAG_PASSIVE_RELATION = 'user_tag_passive_relation:sset';

const REDIS_KEY_USER_BE_GOOD_AT_TAG_IDS = 'user_be_good_at_tag_ids:string';

#TAG
const REDIS_KEY_TAG = 'tag:hash';
const REDIS_KEY_TAG_LIST = 'tag_list:sset';
const REDIS_KEY_TAG_NAME_ID = 'tag_name_id:string';
const REDIS_KEY_TAG_USER_RELATION = 'tag_user_relation:sset';

#FOLLOW TAG

#QUESTION
const REDIS_KEY_QUESTION = 'question:hash';
const REDIS_KEY_QUESTION_BLOCK = 'question_block:string';
const REDIS_KEY_QUESTION_HAS_ANSWERED = 'question_has_answered:string';

const REDIS_KEY_QUESTION_LIKE_USER_LIST = 'question_like_user_list:set';
const REDIS_KEY_QUESTION_HATE_USER_LIST = 'question_hate_user_list:set';
const REDIS_KEY_QUESTION_FOLLOW_USER_LIST = 'question_follow_user_list:set';
const REDIS_KEY_QUESTION_FAVORITE_USER_LIST = 'question_favorite_user_list:set';

#ANSWER
const REDIS_KEY_ANSWER = 'answer:hash';
const REDIS_KEY_ANSWER_LIST = 'answer_list:string';
const REDIS_KEY_ANSWER_LIST_TIME = 'answer_list_time:sset';
const REDIS_KEY_ANSWER_LIST_SCORE = 'answer_list_score:sset';

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

/* xunsearch */
const REDIS_KEY_XUNSEARCH_TAG = 'xunsearch_tag:string';

/**
 * 注意，
 * key 不得为数字，必须为字符串,
 * expire = 0 表示永久存在
 * serializer Redis::SERIALIZER_IGBINARY Redis::SERIALIZER_NONE Redis::SERIALIZER_PHP
 * hash类型的，serializer需要设置为 Redis::SERIALIZER_NONE
 */
return [
    /************************************************/
    #SESSION设置
    REDIS_KEY_SESSION                     => [
        'server'     => $servers['master'],
        'expire'     => 86400,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #应用设置
    REDIS_KEY_SETTING                     => [
        'server'     => $servers['master'],
        'expire'     => 0,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #打点器队列
    REDIS_KEY_COUNTER                     => [
        'server'     => $servers['master'],
        'expire'     => 0,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    REDIS_KEY_COUNTER_SET                 => [
        'server'     => $servers['master'],
        'expire'     => 0,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #通知器队列
    REDIS_KEY_NOTIFIER                    => [
        'server'     => $servers['master'],
        'expire'     => 0,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    REDIS_KEY_NOTIFIER_SET                => [
        'server'     => $servers['master'],
        'expire'     => 0,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #更新器队列
    REDIS_KEY_UPDATER                     => [
        'server'     => $servers['master'],
        'expire'     => 0,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    REDIS_KEY_UPDATER_SET                 => [
        'server'     => $servers['master'],
        'expire'     => 0,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #注册邮件激活
    REDIS_KEY_EMAIL                       => [
        'server'     => $servers['master'],
        'expire'     => 0,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #RBAC权限
    REDIS_KEY_RBAC                        => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #xunsearch
    REDIS_KEY_XUNSEARCH_TAG               => [
        'server'     => $servers['master'],
        'expire'     => 3600,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    /***************************************************/
    #用户数据
    REDIS_KEY_USER                        => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_NONE,
    ],
    #用户名与用户ID间的关系
    REDIS_KEY_USER_USERNAME_USERID        => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #用户好友
    REDIS_KEY_USER_FRIENDS                => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #用户粉丝
    REDIS_KEY_USER_FANS                   => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #用户主动关注的tag
    REDIS_KEY_USER_TAG_RELATION           => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #用户被动关注的tag
    REDIS_KEY_USER_TAG_PASSIVE_RELATION   => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #用户擅长的标签
    REDIS_KEY_USER_BE_GOOD_AT_TAG_IDS     => [
        'server'     => $servers['master'],
        'expire'     => 86400,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    /*************************************************/
    #tag
    REDIS_KEY_TAG                         => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_NONE,
    ],
    REDIS_KEY_TAG_LIST                    => [
        'server'     => $servers['master'],
        'expire'     => 3600 * 8,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #tag name & id
    REDIS_KEY_TAG_NAME_ID                 => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #tag has user top 10
    REDIS_KEY_TAG_USER_RELATION           => [
        'server'     => $servers['master'],
        'expire'     => 3600 * 8,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #question
    REDIS_KEY_QUESTION                    => [
        'server'     => $servers['master'],
        'expire'     => 86400,
        'serializer' => Redis::SERIALIZER_NONE,
    ],
    #question hot latest ... list
    REDIS_KEY_QUESTION_BLOCK              => [
        'server'     => $servers['master'],
        'expire'     => 600,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    REDIS_KEY_QUESTION_HAS_ANSWERED       => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #喜欢该问题的人
    REDIS_KEY_QUESTION_LIKE_USER_LIST     => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #不喜欢该问题的人
    REDIS_KEY_QUESTION_HATE_USER_LIST     => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #关注此问题的人
    REDIS_KEY_QUESTION_FOLLOW_USER_LIST   => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    #收藏此问题的人
    REDIS_KEY_QUESTION_FAVORITE_USER_LIST => [
        'server'     => $servers['master'],
        'expire'     => 86400 * 7,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    /*------------- answer ---------------*/
    REDIS_KEY_ANSWER                      => [
        'server'     => $servers['master'],
        'expire'     => 86400,
        'serializer' => Redis::SERIALIZER_NONE,
    ],
    REDIS_KEY_ANSWER_LIST                 => [
        'server'     => $servers['master'],
        'expire'     => 86400,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    REDIS_KEY_ANSWER_LIST_TIME            => [
        'server'     => $servers['master'],
        'expire'     => 86400,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
    REDIS_KEY_ANSWER_LIST_SCORE           => [
        'server'     => $servers['master'],
        'expire'     => 86400,
        'serializer' => Redis::SERIALIZER_IGBINARY,
    ],
];