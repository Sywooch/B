<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 10:27
 */

namespace common\components;


use common\entities\FavoriteEntity;
use common\entities\PrivateMessageEntity;
use common\entities\QuestionEntity;
use common\entities\UserProfileEntity;
use common\exceptions\ParamsInvalidException;
use Yii;
use yii\base\Object;

class Counter extends BaseCounter
{
    public static function viewPersonalHomePage($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_home_views',
            1
        )->execute();
    }

    public static function beFollowUser($user_id, $multiple = 1)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_be_follow',
            1
        )->multiple($multiple)->execute();
    }

    public static function cancelBeFollowUser($user_id, $multiple = 1)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_be_follow',
            -1
        )->multiple($multiple)->execute();
    }

    public static function followUser($user_id, $multiple = 1)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_user',
            1
        )->multiple($multiple)->execute();
    }

    public static function cancelFollowUser($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_user',
            -1
        )->execute();
    }

    public static function followTag($user_id, $multiple = 1)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_tag',
            1
        )->multiple($multiple)->execute();
    }

    public static function cancelFollowTag($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_tag',
            -1
        )->execute();
    }

    public static function followQuestion($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_question',
            1
        )->execute();
    }

    public static function cancelFollowQuestion($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_question',
            -1
        )->execute();
    }

    public static function addQuestion($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_question',
            1
        )->execute();
    }

    public static function deleteQuestion($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_question',
            -1
        )->execute();
    }


    public static function addQuestionView($question_id)
    {
        return self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_views',
            1
        )->execute();
    }

    public static function addQuestionAnswer($question_id)
    {
        return self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_answer',
            1
        )->execute();
    }

    public static function deleteQuestionAnswer($question_id)
    {
        return self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_answer',
            -1
        )->execute();
    }

    public static function addQuestionFavorite($question_id)
    {
        return self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_favorite',
            1
        )->execute();
    }

    public static function cancelQuestionFavorite($question_id)
    {
        return self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_favorite',
            -1
        )->execute();
    }

    public static function addQuestionFollow($question_id)
    {
        return self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_follow',
            1
        )->execute();
    }

    public static function cancelQuestionFollow($question_id)
    {
        return self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_follow',
            -1
        )->execute();
    }

    public static function addPrivateMessage($private_message_id)
    {
        return self::build()->set(PrivateMessageEntity::tableName(), $private_message_id)->value(
            'count_message',
            1
        )->execute();
    }

    public static function deletePrivateMessage($private_message_id)
    {
        return self::build()->set(
            PrivateMessageEntity::tableName(),
            $private_message_id
        )->value('count_message', -1)->execute();
    }


    public static function addFavorite($favorite_id)
    {
        return self::build()->set(
            FavoriteEntity::tableName(),
            $favorite_id
        )->value('count_favorite', 1)->execute();
    }

    public static function removeFavorite($favorite_id)
    {
        return self::build()->set(
            FavoriteEntity::tableName(),
            $favorite_id
        )->value('count_favorite', -1)->execute();
    }

    public static function addAnswer($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_answer',
            1
        )->execute();
    }

    public static function deleteAnswer($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_answer',
            -1
        )->execute();
    }

    public static function addCommonEdit($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_common_edit',
            1
        )->execute();
    }

    public static function cancelCommonEdit($user_id)
    {
        return self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_common_edit',
            -1
        )->execute();
    }
}