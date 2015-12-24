<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/16
 * Time: 19:07
 */

namespace common\entities;

use common\behaviors\QuestionTagBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\QuestionTag;
use yii\db\ActiveRecord;

class QuestionTagEntity extends QuestionTag
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
            ],
            'follow_user_behavior' => [
                'class' => QuestionTagBehavior::className(),
            ],
        ];
    }
}
