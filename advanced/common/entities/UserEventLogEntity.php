<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/16
 * Time: 11:27
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\models\UserEventLog;
use yii\db\ActiveRecord;

class UserEventLogEntity extends UserEventLog
{
    const ASSOCIATE_TYPE_QUESTION = 'question';
    const ASSOCIATE_TYPE_ANSWER = 'answer';
    const ASSOCIATE_TYPE_ANSWER_COMMENT = 'answer_comment';
    const ASSOCIATE_TYPE_USER = 'user';
    const ASSOCIATE_TYPE_TAG = 'tag';

    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
            ],
        ];
    }
}
