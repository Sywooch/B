<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/29
 * Time: 18:14
 */

namespace common\components;

use common\traits\ErrorTrait;

use yii\base\Object;

class Error extends Object
{
    use ErrorTrait;

    const TYPE_SYSTEM_NORMAL = 'system:normal';
    const TYPE_SYSTEM_PARAMS_IS_EMPTY = 'system:params_is_empty';
    const TYPE_SYSTEM_PARAMS_IS_ERROR = 'system:params_is_error';
    const TYPE_SYSTEM_AR_SAVE_ERROR = 'system:ar_save_error';

    /* user */
    const TYPE_USER_IS_NOT_EXIST = 'user:user_is_not_exist';
    const TYPE_USER_NOT_ALLOW_TO_RESET_COUNT = 'user:not_allow_to_reset_count';

    /* question */
    const TYPE_QUESTION_XUNSEARCH_GET_EXCEPTION = 'question:xunsearch_get_exception';


    /* answer */
    const TYPE_ANSWER_DATA_IS_WORRY = 'answer:data_is_worry';
    const TYPE_ANSWER_ONE_QUESTION_ONE_ANSWER_PER_PEOPLE = 'answer:one_question_one_answer_per_people';
    const TYPE_ANSWER_ENSURE_EXIST_THE_FIRST_EDITION = 'answer:ensure_exist_the_first_edition';
    /* invite answer */
    const TYPE_ANSWER_HAS_BEEN_INVITED = 'answer:has_been_invited';
    /* answer comment*/
    const TYPE_ANSWER_COMMENT_CREATE_FAIL = 'answer_comment:create_fail';

    /* follow user */
    const TYPE_FOLLOW_USER_FOLLOW_TOO_MUCH_USER = 'follow_user:follow_too_much_user';
    const TYPE_FOLLOW_DO_NOT_ALLOW_TO_FOLLOW = 'follow_user:do_not_allow_to_follow';
    /* follow question */
    const TYPE_FOLLOW_QUESTION_FOLLOW_TOO_MUCH_QUESTION = 'follow_question:follow_too_much_question';
    /* follow tag */
    const TYPE_FOLLOW_TAG_FOLLOW_TOO_MUCH_TAG = 'follow_tag:follow_too_much_tag';

    const TYPE_FOLLOW_OVER_MAX_NUMBER = 'follow:over_max_number';


    /* favorite */
    const TYPE_FAVORITE_CATEGORY_DELETE_FAIL = 'favorite:category_delete_fail';

    public static $error = [
        #1000
        'system'          => [
            'normal'          => [1000, true],
            'params_is_empty' => [1001, '参数：%s 不得为空!'],
            'params_is_error' => [1002, '参数：%s 不正确!'],
            'ar_save_error'   => [1003, '模型：%s 保存出错，信息：%s'],
        ],
        #2000
        'user'            => [
            'user_is_not_exist'        => [2000, '当前用户不存在或已被删除！'],
            'not_allow_to_reset_count' => [2001, '不允许重置此字段！'],
        ],
        #3000
        'question'        => [
            'xunsearch_get_exception' => [3000, '搜索服务出现异常！[%d:%s]'],
        ],
        #4000
        'tag'             => [],
        #5000
        'answer'          => [
            'data_is_worry'                      => [5000, '回答数据格式有误'],
            'one_question_one_answer_per_people' => [5001, '每个问题只能回答一次，你可以完善你之前的回复。'],
            'ensure_exist_the_first_edition'     => [5002, '生成默认第一版数据出错'],
            'has_been_invited'                   => [5003, '该用户已经邀请过。'],
        ],
        #6000
        'answer_comment'  => [
            'create_fail' => [6000, '评论失败'],
        ],
        #8000
        'follow_user'     => [
            'follow_too_much_user'   => [8000, '你当前的关注用户的数量%d个，已超过限制，最多%d个用户，请先清理一下。'],
            'do_not_allow_to_follow' => [8001, '当前用户不允许你关注Ta。'],
        ],
        #9000
        'follow_tag'      => [
            'follow_too_much_tag' => [9000, '你当前的关注标签的数量%d个，已超过限制，最多%d个标签，请先清理一下。'],
        ],
        #10000
        'follow_question' => [
            'follow_too_much_question' => [10000, '你当前的关注问题数量已超过限制，最多%d个，请先清理一下。'],
        ],
        #11000
        'favorite'        => [
            'category_delete_fail' => [11001, '当前收藏夹下还有 %d 个收藏项，请先删除这些收藏项。'],
        ],
        'follow' => [
            'over_max_number' => [12000, '你当前的关注问题数量已超过限制。'],
        ],
    ];
}
