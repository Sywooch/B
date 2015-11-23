<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/26
 * Time: 16:33
 */

namespace common\entities;

use common\behaviors\FavoriteRecordBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Counter;
use common\exceptions\NotFoundModelException;
use common\models\FavoriteRecord;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

class FavoriteRecordEntity extends FavoriteRecord
{
    const TYPE_ARTICLE = 'article';
    const TYPE_QUESTION = 'question';

    public function behaviors()
    {
        return [
            'timestamp'                => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],
            'favorite_record_behavior' => [
                'class' => FavoriteRecordBehavior::className(),
            ],
        ];
    }


    public static function getFavoriteSubject($id)
    {
        $model = self::findOne($id);
        if (!$model) {
            throw new NotFoundModelException(self::className());
        }
        $result = null;
        switch ($model->type) {
            case self::TYPE_QUESTION:
                /* @var $question QuestionEntity */
                $question = QuestionEntity::findOne($model->associate_id);
                if ($question) {
                    $result = $question->subject;
                }

                break;
            case self::TYPE_ARTICLE:
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
        $model = self::find()->where(
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