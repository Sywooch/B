<?php

namespace common\entities;

use common\behaviors\FollowQuestionBehavior;
use common\components\Error;
use Yii;
use common\models\FollowQuestion;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class FollowQuestionEntity extends FollowQuestion
{
    const MAX_FOLLOW_NUMBER = 5000;

    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'user_id',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],
            'follow_question_behavior' => [
                'class'      => FollowQuestionBehavior::className(),
            ],
        ];
    }
}
