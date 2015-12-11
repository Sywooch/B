<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/27
 * Time: 10:09
 */

namespace common\entities;

use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\FollowUser;
use Yii;
use yii\db\ActiveRecord;

class FollowUserEntity extends FollowUser
{
    const MAX_FOLLOW_USER_NUMBER = 1000;

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
            ],
        ];
    }
}
