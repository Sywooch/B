<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/26
 * Time: 11:59
 */

namespace common\entities;

use common\behaviors\FavoriteCategoryBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\models\FavoriteCategory;
use yii\db\ActiveRecord;

class FavoriteCategoryEntity extends FavoriteCategory
{
    public function behaviors()
    {
        return [
            'operator'                   => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                ],
            ],
            'timestamp'                  => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_INSERT => 'active_at',
                    ActiveRecord::EVENT_AFTER_UPDATE => 'active_at',
                ],
            ],
            'favorite_category_behavior' => [
                'class' => FavoriteCategoryBehavior::className(),
            ],
        ];
    }

    public function getFavorites()
    {
        return $this->hasMany(FavoriteEntity::className(), ['favorite_category_id' => 'id']);
    }
}