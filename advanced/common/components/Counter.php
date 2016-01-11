<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 10:27
 */

namespace common\components;

use common\config\RedisKey;
use common\entities\AnswerCommentEntity;
use common\entities\AnswerEntity;
use common\entities\FavoriteCategoryEntity;
use common\entities\PrivateMessageEntity;
use common\entities\QuestionEntity;
use common\entities\TagEntity;
use common\entities\UserProfileEntity;
use common\services\AnswerService;
use common\services\QuestionService;
use common\services\TagService;
use common\services\UserService;
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

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_home_views', 1);
        }

        return $result;
    }

    public static function userAddFans($user_id, $multiple = 1)
    {
        Yii::trace('增加用户粉丝数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_fans',
            1
        )->multiple($multiple)->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_fans', 1);
        }

        return $result;
    }

    public static function userCancelFans($user_id, $multiple = 1)
    {
        Yii::trace('减少用户粉丝数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_fans',
            -1
        )->multiple($multiple)->execute();

        if ($result) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_fans', -1);
        }

        return $result;
    }

    public static function userAddFollowUser($user_id, $multiple = 1)
    {
        Yii::trace('增加关注用户数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_user',
            1
        )->multiple($multiple)->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_follow_user', 1);
        }

        return $result;
    }

    public static function userCancelFollowUser($user_id)
    {
        Yii::trace('减少关注用户数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_user',
            -1
        )->execute();


        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_follow_user', -1);
        }

        return $result;
    }

    public static function userAddFollowQuestion($user_id)
    {
        Yii::trace('增加用户问题关注数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_question',
            1
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_follow_question', 1);
        }

        return $result;
    }

    public static function userCancelFollowQuestion($user_id)
    {
        Yii::trace('减少用户问题关注数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_question',
            -1
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_follow_question', -1);
        }

        return $result;
    }

    public static function userAddFollowTag($user_id, $multiple = 1)
    {
        Yii::trace('增加用户关注标签数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_tag',
            1
        )->multiple($multiple)->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_follow_tag', 1);
        }

        return $result;
    }

    public static function userCancelFollowTag($user_id, $multiple = 1)
    {
        Yii::trace('减少用户标签关注数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_follow_tag',
            -1
        )->multiple($multiple)->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_follow_tag', -1);
        }

        return $result;
    }

    public static function userAddQuestion($user_id)
    {
        Yii::trace('增加用户问题提问数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_question',
            1
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_follow_question', 1);
        }

        return $result;
    }

    public static function userDeleteQuestion($user_id)
    {
        Yii::trace('减少用户问题提问数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_question',
            -1
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_question', -1);
        }

        return $result;
    }

    public static function userAddAnswer($user_id)
    {
        Yii::trace('增加用户的问题回答数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_answer',
            1
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_answer', 1);
        }

        return $result;
    }

    public static function userDeleteAnswer($user_id)
    {
        Yii::trace('减少用户的问题回答数量', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_answer',
            -1
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_answer', -1);
        }

        return $result;
    }

    public static function userAddCommonEdit($user_id)
    {
        Yii::trace('增加用户的公共编辑次数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_common_edit',
            1
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_common_edit', 1);
        }

        return $result;
    }

    public static function userCancelCommonEdit($user_id)
    {
        Yii::trace('减少用户的公共编辑次数', 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            'count_common_edit',
            -1
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], 'count_common_edit', -1);
        }

        return $result;
    }

    public static function updateUserScore($user_id, $type, $score)
    {
        Yii::trace(sprintf('更新用户%d %s %s', $user_id, $type, $score), 'counter');

        $result = self::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
            $type,
            $score
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_USER, $user_id], $type, $score);
        }

        return $result;
    }

    //******************************************QUESTION***************************************************/
    public static function questionAddView($question_id)
    {
        Yii::trace('增加问题查看数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_views',
            1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_views', 1);
        }

        return $result;
    }

    public static function questionAddAnswer($question_id)
    {
        Yii::trace('增加问题回答数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_answer',
            1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_answer', 1);
        }

        return $result;
    }

    public static function questionDeleteAnswer($question_id)
    {
        Yii::trace('减少问题回答数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_answer',
            -1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_answer', -1);
        }

        return $result;
    }

    public static function questionAddFavorite($question_id)
    {
        Yii::trace('增加问题收藏数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_favorite',
            1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_favorite', 1);
        }

        return $result;
    }

    public static function questionCancelFavorite($question_id)
    {
        Yii::trace('减少问题收藏数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_favorite',
            -1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_favorite', -1);
        }

        return $result;
    }

    public static function questionAddFollow($question_id)
    {
        Yii::trace('增加问题关注数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_follow',
            1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_follow', 1);
        }

        return $result;
    }

    public static function questionCancelFollow($question_id)
    {
        Yii::trace('减少问题关注数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_follow',
            -1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_follow', -1);
        }

        return $result;
    }

    public static function questionAddLike($question_id)
    {
        Yii::trace('增加问题喜欢数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_like',
            1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_like', 1);
        }

        return $result;
    }

    public static function questionCancelLike($question_id)
    {
        Yii::trace('减少问题喜欢数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_like',
            -1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_like', -1);
        }

        return $result;
    }

    public static function questionAddHate($question_id)
    {
        Yii::trace('增加问题讨厌数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_hate',
            1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_hate', 1);
        }

        return $result;
    }

    public static function questionCancelHate($question_id)
    {
        Yii::trace('减少问题喜欢数量', 'counter');

        $result = self::build()->set(QuestionEntity::tableName(), $question_id)->value(
            'count_hate',
            -1
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_QUESTION, $question_id], 'count_hate', -1);
        }

        return $result;
    }

    //******************************************ANSWER***************************************************/

    public static function answerAddComment($answer_id)
    {
        Yii::trace('增加回答评论数量', 'counter');

        $result = self::build()->set(AnswerEntity::tableName(), $answer_id)->value(
            'count_comment',
            1
        )->execute();

        if ($result && AnswerService::ensureAnswerHasCache($answer_id)) {
            $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
            Yii::$app->redis->hIncrBy($cache_key, 'count_comment', 1);
        }

        return $result;
    }

    public static function answerDeleteComment($answer_id)
    {
        Yii::trace('减少回答评论数量', 'counter');

        $result = self::build()->set(AnswerEntity::tableName(), $answer_id)->value(
            'count_comment',
            -1
        )->execute();

        if ($result && AnswerService::ensureAnswerHasCache($answer_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_ANSWER, $answer_id], 'count_comment', -1);
        }

        return $result;
    }

    public static function answerAddLike($answer_id)
    {
        Yii::trace('增加回答喜欢数量', 'counter');

        $result = self::build()->set(AnswerEntity::tableName(), $answer_id)->value(
            'count_like',
            1
        )->execute();

        if ($result && AnswerService::ensureAnswerHasCache($answer_id)) {
            $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
            Yii::$app->redis->hIncrBy($cache_key, 'count_like', 1);
        }

        return $result;
    }

    public static function answerCancelLike($answer_id)
    {
        Yii::trace('减少回答喜欢数量', 'counter');

        $result = self::build()->set(AnswerEntity::tableName(), $answer_id)->value(
            'count_like',
            -1
        )->execute();

        if ($result && AnswerService::ensureAnswerHasCache($answer_id)) {
            $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
            Yii::$app->redis->hIncrBy($cache_key, 'count_like', -1);
        }

        return $result;
    }

    public static function answerAddHate($answer_id)
    {
        Yii::trace('增加回答讨厌数量', 'counter');

        $result = self::build()->set(AnswerEntity::tableName(), $answer_id)->value(
            'count_hate',
            1
        )->execute();

        if ($result && AnswerService::ensureAnswerHasCache($answer_id)) {
            $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
            Yii::$app->redis->hIncrBy($cache_key, 'count_hate', 1);
        }

        return $result;
    }

    public static function answerCancelHate($answer_id)
    {
        Yii::trace('减少回答讨厌数量', 'counter');

        $result = self::build()->set(AnswerEntity::tableName(), $answer_id)->value(
            'count_hate',
            -1
        )->execute();

        if ($result && AnswerService::ensureAnswerHasCache($answer_id)) {
            $cache_key = [RedisKey::REDIS_KEY_ANSWER, $answer_id];
            Yii::$app->redis->hIncrBy($cache_key, 'count_hate', -1);
        }

        return $result;
    }

    public static function answerCommentAddLike($answer_comment_id)
    {
        Yii::trace('增加评论喜欢数量', 'counter');

        //评论没有使用缓存，不使用队列更新
        $result = self::build()->sync(true)->set(AnswerCommentEntity::tableName(), $answer_comment_id)->value(
            'count_like',
            1
        )->execute();

        return $result;
    }

    public static function answerCommentCancelLike($answer_id)
    {
        Yii::trace('减少评论喜欢数量', 'counter');
        //评论没有使用缓存，不使用队列更新
        $result = self::build()->sync(true)->set(AnswerCommentEntity::tableName(), $answer_id)->value(
            'count_like',
            -1
        )->execute();

        return $result;
    }


    //******************************************MESSAGE***************************************************/


    public static function privateMessageAddMessage($private_message_id)
    {
        Yii::trace('增加私信的通知数量', 'counter');

        return self::build()->set(PrivateMessageEntity::tableName(), $private_message_id)->value(
            'count_message',
            1
        )->execute();
    }

    public static function privateMessageDeleteMessage($private_message_id)
    {
        Yii::trace('减少私信的通知数量', 'counter');

        return self::build()->set(
            PrivateMessageEntity::tableName(),
            $private_message_id
        )->value('count_message', -1)->execute();
    }

    //******************************************FAVORITE***************************************************/

    public static function favoriteCategoryAddFavorite($favorite_category_id)
    {
        Yii::trace('增加收藏夹分类的收藏数量', 'counter');

        if ($favorite_category_id) {
            return self::build()->set(
                FavoriteCategoryEntity::tableName(),
                $favorite_category_id
            )->value('count_favorite', 1)->execute();
        } else {
            return false;
        }
    }

    public static function favoriteCagetoryRemoveFavorite($favorite_category_id)
    {
        Yii::trace('减少收藏夹分类的收藏数量', 'counter');

        if ($favorite_category_id) {
            return self::build()->set(
                FavoriteCategoryEntity::tableName(),
                $favorite_category_id
            )->value('count_favorite', -1)->execute();
        } else {
            return false;
        }
    }

    //******************************************TAG***************************************************/

    public static function tagAddFollow($tag_id)
    {
        Yii::trace('增加标签关注数量', 'counter');

        $result = self::build()->set(TagEntity::tableName(), $tag_id, 'id')->value(
            'count_follow',
            1
        )->execute();

        if ($result && TagService::ensureTagHasCached($tag_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_TAG, $tag_id], 'count_follow', 1);
        }

        return $result;
    }

    public static function tagCancelFollow($tag_id)
    {
        Yii::trace('减少标签关注数量', 'counter');

        $result = self::build()->set(TagEntity::tableName(), $tag_id, 'id')->value(
            'count_follow',
            -1
        )->execute();

        if ($result && TagService::ensureTagHasCached($tag_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_TAG, $tag_id], 'count_follow', -1);
        }

        return $result;
    }

    public static function tagAddUse($tag_id)
    {
        Yii::trace('增加标签使用数量', 'counter');

        $result = self::build()->set(TagEntity::tableName(), $tag_id, 'id')->value(
            'count_use',
            1
        )->execute();

        if ($result && TagService::ensureTagHasCached($tag_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_TAG, $tag_id], 'count_use', 1);
        }

        return $result;
    }

    public static function tagCancelUse($tag_id)
    {
        Yii::trace('减少标签使用数量', 'counter');

        $result = self::build()->set(TagEntity::tableName(), $tag_id, 'id')->value(
            'count_use',
            -1
        )->execute();

        if ($result && TagService::ensureTagHasCached($tag_id)) {
            Yii::$app->redis->hIncrBy([RedisKey::REDIS_KEY_TAG, $tag_id], 'count_use', -1);
        }

        return $result;
    }
}
