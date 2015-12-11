<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 18:45
 */

namespace common\entities;

use common\behaviors\AnswerCommentBehavior;
use common\behaviors\IpBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\AnswerComment;
use yii\db\ActiveRecord;
use Yii;

class AnswerCommentEntity extends AnswerComment
{

    const STATUS_ANONYMOUS = 'yes';
    const STATUS_UNANONYMOUS = 'no';

    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            'ip'        => [
                'class' => IpBehavior::className(),
            ],
            'behavior'  => [
                'class' => AnswerCommentBehavior::className(),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(AnswerEntity::className(), ['id' => 'answer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'created_by']);
    }
}
