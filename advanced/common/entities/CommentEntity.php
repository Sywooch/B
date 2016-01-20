<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 18:45
 */

namespace common\entities;

use common\behaviors\CommentBehavior;
use common\behaviors\IpBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\Comment;
use yii\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

class CommentEntity extends Comment
{

    const STATUS_ANONYMOUS = 'yes';
    const STATUS_UNANONYMOUS = 'no';

    const STATUS_ENABLE = 'enable';
    const STATUS_DISABLE = 'disable';
    const STATUS_PENDING = 'pending';

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
                'class' => CommentBehavior::className(),
            ],
        ];
    }

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'common_edit' => ['update_reason', 'content'],
            ]
        );
    }

    /**
     * @return AnswerEntity
     */
    public function getAnswer()
    {
        return AnswerEntity::find()->where(['id' => $this->associate_id])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'created_by']);
    }
}
