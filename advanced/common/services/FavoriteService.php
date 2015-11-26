<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 15:04
 */

namespace common\services;


use common\components\Counter;
use common\entities\FavoriteRecordEntity;
use common\exceptions\NotFoundModelException;

class FavoriteService extends BaseService
{
    public static function getFavoriteSubject($id)
    {
        $model = FavoriteRecordEntity::findOne($id);
        if (!$model) {
            throw new NotFoundModelException(FavoriteRecordEntity::className());
        }
        $result = null;
        switch ($model->type) {
            case FavoriteRecordEntity::TYPE_QUESTION:
                /* @var $question QuestionEntity */
                $question = QuestionEntity::findOne($model->associate_id);
                if ($question) {
                    $result = $question->subject;
                }

                break;
            case FavoriteRecordEntity::TYPE_ARTICLE:
                #todo
                throw new Exception('todo');
                break;

            default:
                throw new Exception('todo');
        }

        return $result;
    }

    public static function removeFavoriteRecord($type, $associate_id)
    {
        $model = FavoriteRecordEntity::find()->where(
            [
                'type'         => $type,
                'associate_id' => $associate_id,
            ]
        )->all();

        $favorite_ids = [];
        foreach ($model as $favorite_record) {
            if ($favorite_record->delete()) {
                $favorite_ids[] = $favorite_record->favorite_id;
            }
        }

        foreach ($favorite_ids as $favorite_id) {
            Counter::addFavorite($favorite_id);
        }

        return true;
    }
}