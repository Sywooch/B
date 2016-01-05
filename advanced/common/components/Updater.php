<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/22
 * Time: 9:45
 */

namespace common\components;

use common\config\RedisKey;
use common\entities\AnswerEntity;
use common\entities\FavoriteCategoryEntity;
use common\entities\QuestionEntity;
use common\entities\UserProfileEntity;
use common\services\AnswerService;
use common\services\QuestionService;
use common\services\UserService;
use Yii;

class Updater extends BaseUpdater
{
    public static function clearNotifyCount($user_id)
    {
        $result = self::build()->table(UserProfileEntity::tableName())->set(
            [
                'count_notification' => 0,
            ]
        )->execute();

        if ($result && UserService::ensureUserHasCached($user_id)) {
            Yii::$app->redis->hSet([RedisKey::REDIS_KEY_USER, $user_id], 'count_notification', 0);
        }

        return $result;
    }

    public static function updateQuestionContent($id, $content)
    {
        $result = self::build()->sync(true)->table(QuestionEntity::tableName())->set(['content' => $content])->where(
            ['id' => $id]
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($id)) {
            QuestionService::updateQuestionCache($id, ['content' => $content]);
        }

        return $result;
    }

    public static function updateQuestionActiveAt($id, $updated_at)
    {
        $result = self::build()->sync(true)->table(QuestionEntity::tableName())->set(
            ['updated_at' => $updated_at]
        )->where(
            ['id' => $id]
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($id)) {
            QuestionService::updateQuestionCache($id, ['updated_at' => $updated_at]);
        }

        return $result;
    }

    public static function updateFavoriteCategoryActiveAt($id, $updated_at)
    {
        $result = self::build()->sync(true)->table(FavoriteCategoryEntity::tableName())->set(
            ['updated_at' => $updated_at]
        )->where(
            ['id' => $id]
        )->execute();

        //no redis cache
        return $result;
    }

    public static function setQuestionAnonymous($question_id)
    {
        $result = self::build()->sync(true)->table(QuestionEntity::tableName())->set(
            ['is_anonymous' => QuestionEntity::STATUS_ANONYMOUS]
        )->where(
            ['id' => $question_id]
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            QuestionService::updateQuestionCache(
                $question_id,
                [
                    'is_anonymous' => QuestionEntity::STATUS_ANONYMOUS,
                ]
            );
        }

        return $result;
    }

    public static function cancelQuestionAnonymous($question_id)
    {
        $result = self::build()->sync(true)->table(QuestionEntity::tableName())->set(
            ['is_anonymous' => QuestionEntity::STATUS_UNANONYMOUS]
        )->where(
            ['id' => $question_id]
        )->execute();

        if ($result && QuestionService::ensureQuestionHasCache($question_id)) {
            QuestionService::updateQuestionCache(
                $question_id,
                [
                    'is_anonymous' => QuestionEntity::STATUS_UNANONYMOUS,
                ]
            );
        }

        return $result;
    }

    public static function setAnswerAnonymous($answer_id)
    {
        $result = self::build()->sync(true)->table(AnswerEntity::tableName())->set(
            ['is_anonymous' => AnswerEntity::STATUS_ANONYMOUS]
        )->where(
            ['id' => $answer_id]
        )->execute();

        if ($result && AnswerService::ensureAnswerHasCache($answer_id)) {
            AnswerService::updateAnswerCache(
                $answer_id,
                [
                    'is_anonymous' => AnswerEntity::STATUS_ANONYMOUS,
                ]
            );
        }

        return $result;
    }

    public static function cancelAnswerAnonymous($answer_id)
    {
        $result = self::build()->sync(true)->table(AnswerEntity::tableName())->set(
            ['is_anonymous' => AnswerEntity::STATUS_UNANONYMOUS]
        )->where(
            ['id' => $answer_id]
        )->execute();

        if ($result && AnswerService::ensureAnswerHasCache($answer_id)) {
            AnswerService::updateAnswerCache(
                $answer_id,
                [
                    'is_anonymous' => AnswerEntity::STATUS_UNANONYMOUS,
                ]
            );
        }

        return $result;
    }
}
