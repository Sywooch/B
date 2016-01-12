<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/16
 * Time: 17:00
 */

namespace common\entities;

use common\behaviors\AnswerBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\helpers\ArrayHelper;
use common\models\Answer;
use Yii;
use yii\db\ActiveRecord;

class AnswerEntity extends Answer
{
    const STATUS_FOLD = 'yes';
    const STATUS_UNFOLD = 'no';

    const STATUS_ANONYMOUS = 'yes';
    const STATUS_UNANONYMOUS = 'no';

    const TYPE_ANSWER = 'answer';
    const TYPE_REFERENCED = 'referenced';


    public $update_reason;

    public function behaviors()
    {
        return [
            'operator'        => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
            ],
            'timestamp'       => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            'answer_behavior' => [
                'class' => AnswerBehavior::className(),
            ],
        ];
    }

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'common_edit' => ['update_reason'],
            ]
        );
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['update_reason'], 'string', 'max' => 255, 'on' => 'common_edit'];

        return $rules;
    }


    public function addAnswer($data)
    {
        if ($this->load($data) && $this->save()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(QuestionEntity::className(), ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerComments()
    {
        return $this->hasMany(AnswerCommentEntity::className(), ['answer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerUsefulLogs()
    {
        return $this->hasMany(AnswerUsefulLogEntity::className(), ['answer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(UserEntity::className(), ['id' => 'user_id'])->viaTable(
            'answer_useful_log',
            [
                'answer_id' => 'id',
            ]
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerVersions()
    {
        return $this->hasMany(AnswerVersionEntity::className(), ['answer_id' => 'id']);
    }
}
