<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/24
 * Time: 17:13
 */

namespace common\config;

use Redis;

/**
 * Class RedisKey
 * 注意，
 * key 不得为数字，必须为字符串,
 * expire = 0 表示永久存在
 * serializer Redis::SERIALIZER_NONE Redis::SERIALIZER_NONE Redis::SERIALIZER_PHP
 * 除了string\list类型的，serializer需要设置为 Redis::SERIALIZER_IGBINARY,可以直接保存数组格式。其他均是SERIALIZER_NONE,避免出错
 * @package common\config
 */
class RedisKey
{
    #user
    const REDIS_KEY_USER = 'user:hash';
    const REDIS_KEY_USER_USERNAME_USERID = 'username_userid:string';

    const REDIS_KEY_USER_FRIENDS = 'user_friends:set';
    const REDIS_KEY_USER_FANS_LIST = 'user_fans_list:sset';//关注此用户的人
    const REDIS_KEY_USER_FRIEND_LIST = 'user_friend_list:sset';//用户关注的人，即用户好友

    const REDIS_KEY_USER_TAG_RELATION = 'user_tag_relation:sset';#用户主动关注的标签
    const REDIS_KEY_USER_TAG_PASSIVE_RELATION = 'user_tag_passive_relation:sset';#用户被动关注的标签

    const REDIS_KEY_USER_IS_GOOD_AT_TAG_IDS = 'user_is_good_at_tag_ids:string';#用户擅长的标签

    const REDIS_KEY_USER_GRADE_RULE = 'user_grade_rule:string';#用户等级规则
    const REDIS_KEY_USER_SCORE_RULE = 'user_score_rule:string';#用户积分变动规则
    const REDIS_KEY_USER_EVENT = 'user_event:hash';#用户事件
    const REDIS_KEY_USER_EVENT_All = 'user_event_all:string';#用户事件列表


    #TAG
    const REDIS_KEY_TAG = 'tag:hash';
    const REDIS_KEY_TAG_LIST = 'tag_list:sset';
    const REDIS_KEY_TAG_NAME_ID = 'tag_name_id:string';#标签名称与ID
    const REDIS_KEY_TAG_WHICH_USER_IS_GOOD_AT = 'tag_which_user_is_good_at:string';#擅长此标签的用户
    const REDIS_KEY_RELATE_TAG = 'relate_tag:string';#关联标签

    const REDIS_KEY_TAG_USER_RELATION = 'tag_user_relation:sset';#标签与用户的关注
    const REDIS_KEY_TAG_FOLLOW_USER_LIST = 'tag_follow_user_list:sset';//关注此标签的人

    #FOLLOW TAG

    #QUESTION
    const REDIS_KEY_QUESTION = 'question:hash';
    const REDIS_KEY_QUESTION_BLOCK = 'question_block:string';
    const REDIS_KEY_QUESTION_HAS_ANSWERED = 'question_has_answered:string';

    const REDIS_KEY_QUESTION_LIKE_USER_LIST = 'question_like_user_list:set';//喜欢此问题的人
    const REDIS_KEY_QUESTION_HATE_USER_LIST = 'question_hate_user_list:set';//不喜欢此问题的人
    const REDIS_KEY_QUESTION_FOLLOW_USER_LIST = 'question_follow_user_list:sset';//关注此问题的人
    const REDIS_KEY_QUESTION_FAVORITE_USER_LIST = 'question_favorite_user_list:sset';//收藏此问题的人

    #ANSWER
    const REDIS_KEY_ANSWER = 'answer:hash';
    const REDIS_KEY_ANSWER_LIST = 'answer_list:string';
    const REDIS_KEY_ANSWER_LIST_TIME = 'answer_list_time:sset';
    const REDIS_KEY_ANSWER_LIST_SCORE = 'answer_list_score:sset';

    #VOTE
    const REDIS_KEY_QUESTION_VOTE_USER_LIST = 'vote_question_user_list:sset';//投票此问题的人
    const REDIS_KEY_ANSWER_VOTE_USER_LIST = 'vote_answer_user_list:sset';//投票此答案的人
    const REDIS_KEY_ANSWER_COMMENT_VOTE_USER_LIST = 'vote_answer_comment_user_list:sset';//投票此答案的人
    const REDIS_KEY_ARTICLE_VOTE_USER_LIST = 'vote_article_user_list:sset';//投票此文章的人

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

    public static $servers = [
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

    public static function buildConfig()
    {
        return [
            /*------------- system ---------------*/
            #SESSION设置
            self::REDIS_KEY_SESSION                       => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #应用设置
            self::REDIS_KEY_SETTING                       => [
                'server'     => self::$servers['master'],
                'expire'     => 0,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #打点器队列
            self::REDIS_KEY_COUNTER                       => [
                'server'     => self::$servers['master'],
                'expire'     => 0,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            self::REDIS_KEY_COUNTER_SET                   => [
                'server'     => self::$servers['master'],
                'expire'     => 0,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #通知器队列
            self::REDIS_KEY_NOTIFIER                      => [
                'server'     => self::$servers['master'],
                'expire'     => 0,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            self::REDIS_KEY_NOTIFIER_SET                  => [
                'server'     => self::$servers['master'],
                'expire'     => 0,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #更新器队列
            self::REDIS_KEY_UPDATER                       => [
                'server'     => self::$servers['master'],
                'expire'     => 0,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            self::REDIS_KEY_UPDATER_SET                   => [
                'server'     => self::$servers['master'],
                'expire'     => 0,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #注册邮件激活
            self::REDIS_KEY_EMAIL                         => [
                'server'     => self::$servers['master'],
                'expire'     => 0,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #RBAC权限
            self::REDIS_KEY_RBAC                          => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #xunsearch
            self::REDIS_KEY_XUNSEARCH_TAG                 => [
                'server'     => self::$servers['master'],
                'expire'     => 3600,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            /*------------- user ---------------*/
            #用户数据
            self::REDIS_KEY_USER                          => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #用户名与用户ID间的关系
            self::REDIS_KEY_USER_USERNAME_USERID          => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #用户好友
            self::REDIS_KEY_USER_FRIENDS                  => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #关注此用户的人，即此用户的粉丝
            self::REDIS_KEY_USER_FANS_LIST                => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #用户关注的人，即此用户的好友
            self::REDIS_KEY_USER_FRIEND_LIST              => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #用户主动关注的tag
            self::REDIS_KEY_USER_TAG_RELATION             => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #用户被动关注的tag
            self::REDIS_KEY_USER_TAG_PASSIVE_RELATION     => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #用户擅长的标签ID
            self::REDIS_KEY_USER_IS_GOOD_AT_TAG_IDS       => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            self::REDIS_KEY_USER_GRADE_RULE               => [
                'server'     => self::$servers['master'],
                'expire'     => 864000,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            self::REDIS_KEY_USER_SCORE_RULE               => [
                'server'     => self::$servers['master'],
                'expire'     => 864000,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            self::REDIS_KEY_USER_EVENT                    => [
                'server'     => self::$servers['master'],
                'expire'     => 864000,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            self::REDIS_KEY_USER_EVENT_All                => [
                'server'     => self::$servers['master'],
                'expire'     => 864000,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            /*------------- tag ---------------*/
            #tag
            self::REDIS_KEY_TAG                           => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            self::REDIS_KEY_TAG_LIST                      => [
                'server'     => self::$servers['master'],
                'expire'     => 3600 * 8,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #tag name & id
            self::REDIS_KEY_TAG_NAME_ID                   => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #tag has user top 10
            self::REDIS_KEY_TAG_USER_RELATION             => [
                'server'     => self::$servers['master'],
                'expire'     => 3600 * 8,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #擅长此标签的人
            self::REDIS_KEY_TAG_WHICH_USER_IS_GOOD_AT     => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #关联标签
            self::REDIS_KEY_RELATE_TAG                    => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #关注此标签的人
            self::REDIS_KEY_TAG_FOLLOW_USER_LIST          => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            /*------------- question ---------------*/
            #question
            self::REDIS_KEY_QUESTION                      => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #question hot latest ... list
            self::REDIS_KEY_QUESTION_BLOCK                => [
                'server'     => self::$servers['master'],
                'expire'     => 600,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            self::REDIS_KEY_QUESTION_HAS_ANSWERED         => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            #喜欢该问题的人 todo 未使用
            self::REDIS_KEY_QUESTION_LIKE_USER_LIST       => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #不喜欢该问题的人 todo 未使用
            self::REDIS_KEY_QUESTION_HATE_USER_LIST       => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #关注此问题的人
            self::REDIS_KEY_QUESTION_FOLLOW_USER_LIST     => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            #收藏此问题的人
            self::REDIS_KEY_QUESTION_FAVORITE_USER_LIST   => [
                'server'     => self::$servers['master'],
                'expire'     => 86400 * 7,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            /*------------- answer ---------------*/
            self::REDIS_KEY_ANSWER                        => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            // todo 未使用
            self::REDIS_KEY_ANSWER_LIST                   => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_IGBINARY,
            ],
            self::REDIS_KEY_ANSWER_LIST_TIME              => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            self::REDIS_KEY_ANSWER_LIST_SCORE             => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            /*------------- vote ---------------*/
            self::REDIS_KEY_QUESTION_VOTE_USER_LIST       => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            self::REDIS_KEY_ANSWER_VOTE_USER_LIST         => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            self::REDIS_KEY_ANSWER_COMMENT_VOTE_USER_LIST => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
            // todo 未使用
            self::REDIS_KEY_ARTICLE_VOTE_USER_LIST        => [
                'server'     => self::$servers['master'],
                'expire'     => 86400,
                'serializer' => Redis::SERIALIZER_NONE,
            ],
        ];
    }
}
