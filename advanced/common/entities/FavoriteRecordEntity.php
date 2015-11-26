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
use common\models\FavoriteRecord;
use Yii;
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
}
