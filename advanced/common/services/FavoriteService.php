<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 15:04
 */

namespace common\services;


use common\components\Counter;
use common\entities\FavoriteCategoryEntity;
use common\entities\FavoriteEntity;
use common\exceptions\NotFoundModelException;

class FavoriteService extends BaseService
{
    public static function getFavoriteSubject($id)
    {
        $model = FavoriteEntity::findOne($id);
        if (!$model) {
            throw new NotFoundModelException(FavoriteEntity::className());
        }
        $result = null;
        switch ($model->type) {
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

    public static function removeFavoriteByAssociateId($type, $associate_id)
    {
        $model = FavoriteEntity::find()->where(
            [
                'type'         => $type,
                'associate_id' => $associate_id,
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
            Counter::removeFavorite($favorite_category_id);
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
            Counter::removeFavorite($favorite_category_id);
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
}
