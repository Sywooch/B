<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/14
 * Time: 14:45
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\behaviors\VoteBehavior;
use common\models\Vote;
use yii\db\ActiveRecord;

class VoteEntity extends Vote
{
    const TYPE_QUESTION = 'question';
    const TYPE_ANSWER = 'answer';
    const TYPE_ARTICLE = 'article';
    const TYPE_COMMENT = 'comment';

    const VOTE_YES = 'yes';
    const VOTE_NO = 'no';

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
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'created_at',
                ],
            ],
            'vote'      => [
                'class' => VoteBehavior::className(),
            ],
        ];
    }
}
