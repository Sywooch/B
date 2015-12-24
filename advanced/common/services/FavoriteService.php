<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 15:04
 */

namespace common\services;

use common\components\Counter;
use common\components\Error;
use common\entities\FavoriteCategoryEntity;
use common\entities\FavoriteEntity;
use common\entities\QuestionEntity;
use common\exceptions\NotFoundModelException;
use common\helpers\TimeHelper;
use Exception;
use Yii;

class FavoriteService extends BaseService
{
    const MAX_FAVORITE_QUESTION_COUNT_BY_USING_CACHE = 1000;

    public static function addFavoriteQuestion($question_id, $user_id)
    {
        $model = new FavoriteEntity();
        $model->created_by = $user_id;
        $model->associate_type = FavoriteEntity::TYPE_QUESTION;
        $model->associate_id = $question_id;

        if ($model->save()) {
            return true;
        } else {
            return Error::set(
                Error::TYPE_SYSTEM_AR_SAVE_ERROR,
                [
                    FavoriteEntity::className(),
                    var_export(
                        $model->getErrors(),
                        true
                    ),
                ]
            );
        }
    }

    public static function getFavoriteSubject($id)
    {
        /* @var $model FavoriteEntity */
        $model = FavoriteEntity::findOne($id);
        if (!$model) {
            throw new NotFoundModelException(FavoriteEntity::className(), $id);
        }
        $result = null;
        switch ($model->associate_type) {
            case FavoriteEntity::TYPE_QUESTION:
                /* @var $question QuestionEntity */
                $question = QuestionEntity::findOne($model->associate_id);
                if ($question) {
                    $result = $question->subject;
                }
                
                break;
            case FavoriteEntity::TYPE_ARTICLE:
                #todo
                throw new Exception('todo');
                break;
            
            default:
                throw new Exception('todo');
        }
        
        return $result;
    }

    /**
     * 取消问题收藏
     * @param $question_id
     * @param $user_id
     * @return bool
     */
    public static function removeQuestionFavorite($question_id, $user_id)
    {
        return self::removeFavoriteByAssociateId(FavoriteEntity::TYPE_QUESTION, $question_id, $user_id);
    }

    /**
     * 移除收藏项
     * @param      $associate_type
     * @param      $associate_id
     * @param null $user_id
     * @return bool
     * @throws \Exception
     */
    public static function removeFavoriteByAssociateId($associate_type, $associate_id, $user_id = null)
    {
        $model = FavoriteEntity::find()->where(
            [
                'associate_type' => $associate_type,
                'associate_id'   => $associate_id,
            ]
        )->filterWhere(['created_by' => $user_id])->all();

        /* @var $favorite FavoriteEntity */
        foreach ($model as $favorite) {
            $favorite->delete();
        }
        
        return true;
    }
    
    public static function removeFavoriteByIds(array $ids)
    {
        $model = FavoriteEntity::find()->where(
            [
                'id' => $ids,
            ]
        )->all();
        
        $favorite_category_ids = [];
        /* @var $favorite FavoriteEntity */
        foreach ($model as $favorite) {
            if ($favorite->delete()) {
                $favorite_category_ids[] = $favorite->favorite_category_id;
            }
        }
        
        foreach ($favorite_category_ids as $favorite_category_id) {
            Counter::favoriteCagetoryRemoveFavorite($favorite_category_id);
        }
        
        return true;
    }
    
    public static function getUserFavoriteCategoryList($user_id, $page_no = 1, $page_size = 20)
    {
        return FavoriteCategoryEntity::find()->where(
            [
                'created_by' => $user_id,
            ]
        )->limiter($page_no, $page_size)->asArray()->all();
    }
    
    public static function getUserFavoriteList($user_id, $page_no = 1, $page_size = 20)
    {
        return FavoriteEntity::find()->where(
            [
                'created_by' => $user_id,
            ]
        )->limiter($page_no, $page_size)->asArray()->all();
    }


    /**
     * 添加收藏此问题的人的缓存
     * 如果此问题收藏人数已超过1000，则不使用缓存
     * @param $question_id
     * @param $user_id
     * @return mixed
     * @throws NotFoundModelException
     */
    public static function addUserOfFavoriteQuestionCache($question_id, $user_id)
    {
        $question = QuestionService::getQuestionByQuestionId($question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $question_id);
        }

        $cache_key = [REDIS_KEY_QUESTION_FAVORITE_USER_LIST, $question_id];

        if ($question['count_favorite'] < self::MAX_FAVORITE_QUESTION_COUNT_BY_USING_CACHE) {
            //小于1000，则使用缓存，大于，则不处理。
            self::ensureUserOfFavoriteQuestionHasCached($cache_key, $question_id);

            $insert_cache_data = [];
            //存在则判断是否已存在集合中
            $cache_data = Yii::$app->redis->zScore($cache_key, $user_id);

            if ($cache_data === false) {
                $insert_cache_data[] = ['create_at' => TimeHelper::getCurrentTime(), 'user_id' => $user_id];
            }

            if ($insert_cache_data) {
                //添加到缓存中
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }

        return true;
    }

    /**
     * 移除收藏此问题的用户
     * @param $question_id
     * @param $user_id
     * @return bool
     * @throws NotFoundModelException
     */
    public static function removeUserOfFavoriteQuestionCache($question_id, $user_id)
    {
        $question = QuestionService::getQuestionByQuestionId($question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $question_id);
        }

        $cache_key = [REDIS_KEY_QUESTION_FAVORITE_USER_LIST, $question_id];

        if (Yii::$app->redis->zScore($cache_key, $user_id) !== false) {
            //存在则移除
            return Yii::$app->redis->zRem($cache_key, $user_id);
        } else {
            return true;
        }
    }

    public static function ensureUserOfFavoriteQuestionHasCached($cache_key, $question_id)
    {
        if (Yii::$app->redis->zCard($cache_key) == 0) {
            $insert_cache_data = FavoriteEntity::find()->select(
                [
                    'created_at',
                    'user_id' => 'created_by',
                ]
            )->where(
                [
                    'associate_type' => FavoriteEntity::TYPE_QUESTION,
                    'associate_id'   => $question_id,
                ]
            )->asArray()->all();

            if ($insert_cache_data) {
                return Yii::$app->redis->batchZAdd($cache_key, $insert_cache_data);
            }
        }
    }

    /**
     * 判断用户是否已收藏本问题
     * @param $favorite_question_id
     * @param $user_id
     * @return bool
     * @throws NotFoundModelException
     */
    public static function checkUseIsFavoriteQuestion($favorite_question_id, $user_id)
    {
        $question = QuestionService::getQuestionByQuestionId($favorite_question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $favorite_question_id);
        }

        $cache_key = [REDIS_KEY_QUESTION_FAVORITE_USER_LIST, $favorite_question_id];

        if ($question['count_favorite'] < self::MAX_FAVORITE_QUESTION_COUNT_BY_USING_CACHE) {
            self::ensureUserOfFavoriteQuestionHasCached($cache_key, $favorite_question_id);

            //小于1000，则使用缓存
            $result = Yii::$app->redis->zScore($cache_key, $user_id);
        } else {
            //大于则查询数据库
            $result = FavoriteEntity::find()->where(
                [
                    'associate_type' => FavoriteEntity::TYPE_QUESTION,
                    'associate_id'   => $favorite_question_id,
                    'created_by'     => $user_id,
                ]
            )->count(1);
        }

        return $result;
    }
}
