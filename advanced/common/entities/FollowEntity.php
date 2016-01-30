<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-01-27
 * Time: 11:34
 */

namespace common\entities;


use common\behaviors\FollowBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\models\Follow;
use Yii;
use yii\db\ActiveRecord;

class FollowEntity extends Follow
{
    const MAX_FOLLOW_QUESTION_NUMBER = 5000;
    const MAX_FOLLOW_TAG_NUMBER = 5000;
    const MAX_FOLLOW_USER_NUMBER = 2000;

    public function behaviors()
    {
        return [
            'operator'                 => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
            ],
            'timestamp'                => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            'follow_question_behavior' => [
                'class' => FollowBehavior::className(),
            ],
        ];
    }
}
