<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/26
 * Time: 16:33
 */

namespace common\entities;

use common\behaviors\FavoriteBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\models\Favorite;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class FavoriteEntity
 * @package common\entities
 * @property \common\entities\FavoriteEntity owner
 */
class FavoriteEntity extends Favorite
{
    const TYPE_ARTICLE = 'article';
    const TYPE_QUESTION = 'question';
    const TYPE_ANSWER = 'answer';

    public function behaviors()
    {
        return [
            'operator'          => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                ],
            ],
            'timestamp'                => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],
            'favorite_behavior' => [
                'class' => FavoriteBehavior::className(),
            ],
        ];
    }
}
