<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 10:27
 */

namespace common\components;

use common\entities\AnswerEntity;
use common\entities\FavoriteEntity;
use common\entities\PrivateMessageEntity;
use common\entities\QuestionEntity;
use common\entities\UserEntity;
use common\entities\UserProfileEntity;
use Yii;

class Counter extends BaseCounter
{
    //******************************************USER***************************************************/
    public static function viewPersonalHomePage($user_id)
    {
        Yii::trace('增加个人主页查看数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_home_views',
            1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_home_views', 1);
        }

        return $result;
    }

    public static function beFollowUser($user_id, $multiple = 1)
    {
        Yii::trace('增加被关注用户数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_be_follow',
            1
        )->multiple($multiple)->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_be_follow', 1);
        }

        return $result;
    }

    public static function cancelBeFollowUser($user_id, $multiple = 1)
    {
        Yii::trace('减少被关注用户数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_be_follow',
            -1
        )->multiple($multiple)->execute();

        if ($result) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_be_follow', -1);
        }

        return $result;
    }

    public static function followUser($user_id, $multiple = 1)
    {
        Yii::trace('增加关注用户数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_user',
            1
        )->multiple($multiple)->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_follow_user', 1);
        }

        return $result;
    }

    public static function cancelFollowUser($user_id)
    {
        Yii::trace('减少关注用户数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_user',
            -1
        )->execute();


        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_follow_user', -1);
        }

        return $result;
    }

    public static function followTag($user_id, $multiple = 1)
    {
        Yii::trace('增加用户关注标签数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_tag',
            1
        )->multiple($multiple)->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_follow_tag', 1);
        }

        return $result;
    }

    public static function cancelFollowTag($user_id)
    {
        Yii::trace('减少用户关注标签数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_tag',
            -1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_follow_tag', -1);
        }

        return $result;
    }

    public static function followQuestion($user_id)
    {
        Yii::trace('增加用户问题关注数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_question',
            1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_follow_question', 1);
        }

        return $result;
    }

    public static function cancelFollowQuestion($user_id)
    {
        Yii::trace('减少用户问题关注数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_question',
            -1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_follow_question', -1);
        }

        return $result;
    }

    public static function addQuestion($user_id)
    {
        Yii::trace('增加用户问题提问数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_question',
            1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_follow_question', 1);
        }

        return $result;
    }

    public static function deleteQuestion($user_id)
    {
        Yii::trace('减少用户问题提问数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_question',
            -1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_question', -1);
        }

        return $result;
    }

    public static function addAnswer($user_id)
    {
        Yii::trace('增加用户的问题回答数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_answer',
            1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_answer', 1);
        }

        return $result;
    }

    public static function deleteAnswer($user_id)
    {
        Yii::trace('减少用户的问题回答数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_answer',
            -1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_answer', -1);
        }

        return $result;
    }

    public static function addCommonEdit($user_id)
    {
        Yii::trace('增加用户的公共编辑次数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_common_edit',
            1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_common_edit', 1);
        }

        return $result;
    }

    public static function cancelCommonEdit($user_id)
    {
        Yii::trace('减少用户的公共编辑次数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_common_edit',
            -1
        )->execute();

        if ($result && UserEntity::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_USER, $user_id], 'count_common_edit', -1);
        }

        return $result;
    }

    //******************************************QUESTION***************************************************/
    public static function addQuestionView($question_id)
    {
        Yii::trace('增加问题查看数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_views',
            1
        )->execute();

        if ($result && QuestionEntity::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_QUESTION, $question_id], 'count_views', 1);
        }

        return $result;
    }

    public static function addQuestionAnswer($question_id)
    {
        Yii::trace('增加问题回答数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_answer',
            1
        )->execute();

        if ($result && QuestionEntity::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_QUESTION, $question_id], 'count_answer', 1);
        }

        return $result;
    }

    public static function deleteQuestionAnswer($question_id)
    {
        Yii::trace('减少问题回答数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_answer',
            -1
        )->execute();

        if ($result && QuestionEntity::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_QUESTION, $question_id], 'count_answer', -1);
        }

        return $result;
    }

    public static function addQuestionFavorite($question_id)
    {
        Yii::trace('增加问题收藏数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_favorite',
            1
        )->execute();

        if ($result && QuestionEntity::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_QUESTION, $question_id], 'count_favorite', 1);
        }

        return $result;
    }

    public static function cancelQuestionFavorite($question_id)
    {
        Yii::trace('减少问题收藏数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_favorite',
            -1
        )->execute();

        if ($result && QuestionEntity::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_QUESTION, $question_id], 'count_favorite', -1);
        }

        return $result;
    }

    public static function addQuestionFollow($question_id)
    {
        Yii::trace('增加问题关注数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_follow',
            1
        )->execute();

        if ($result && QuestionEntity::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_QUESTION, $question_id], 'count_follow', 1);
        }

        return $result;
    }

    public static function cancelQuestionFollow($question_id)
    {
        Yii::trace('减少问题关注数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_follow',
            -1
        )->execute();

        if ($result && QuestionEntity::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_QUESTION, $question_id], 'count_follow', -1);
        }

        return $result;
    }

    //******************************************ANSWER***************************************************/

    public static function addAnswerComment($answer_id)
    {
        Yii::trace('增加回答评论数量', 'counter');

        $result = self::build()->set(AnswerEntity::tableName(), $answer_id)->value(
            'count_comment',
            1
        )->execute();

        if ($result && AnswerEntity::ensureAnswerHasCache($answer_id)) {
            $cache_key = [REDIS_KEY_ANSWER, $answer_id];
            Yii::$app->redis->hIncrBy($cache_key, 'count_comment', 1);
        }

        return $result;
    }

    public static function deleteAnswerComment($answer_id)
    {
        Yii::trace('减少回答评论数量', 'counter');

        $result = self::build()->set(AnswerEntity::tableName(), $answer_id)->value(
            'count_comment',
            -1
        )->execute();

        if ($result && AnswerEntity::ensureAnswerHasCache($answer_id)) {
            Yii::$app->redis->hIncrBy([REDIS_KEY_ANSWER, $answer_id], 'count_comment', -1);
        }

        return $result;
    }

    //******************************************MESSAGE***************************************************/


    public static function addPrivateMessage($private_message_id)
    {
        Yii::trace('增加私信的通知数量', 'counter');

        return self::build()->set(PrivateMessageEntity::tableName(), $private_message_id)->value(
            'count_message',
            1
        )->execute();
    }

    public static function deletePrivateMessage($private_message_id)
    {
        Yii::trace('减少私信的通知数量', 'counter');

        return self::build()->set(
            PrivateMessageEntity::tableName(),
            $private_message_id
        )->value('count_message', -1)->execute();
    }

    //******************************************FAVORITE***************************************************/

    public static function addFavorite($favorite_id)
    {
        Yii::trace('增加用户的问题收藏数量', 'counter');

        return self::build()->set(
            FavoriteEntity::tableName(),
            $favorite_id
        )->value('count_favorite', 1)->execute();
    }

    public static function removeFavorite($favorite_id)
    {
        Yii::trace('减少用户的问题收藏数量', 'counter');

        return self::build()->set(
            FavoriteEntity::tableName(),
            $favorite_id
        )->value('count_favorite', -1)->execute();
    }
}
