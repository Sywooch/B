<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/26
 * Time: 11:59
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\models\Favorite;
use yii\db\ActiveRecord;

class FavoriteEntity extends Favorite
{
    public function behaviors()
    {
        return [
            'operator'          => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                ],
            ],
        ];
    }
}