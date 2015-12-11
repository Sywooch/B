<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/27
 * Time: 17:04
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Counter;
use common\models\QuestionReview;
use yii\db\ActiveRecord;
use Yii;

class QuestionReviewEntity extends QuestionReview
{

    const STATUS_PROGRESS = 'progress';
    const STATUS_COMPLETE = 'complete';
    const STATUS_OVERTIME = 'overtime';
    
    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            /*'question_behavior' => [
                'class' => QuestionBehavior::className(),
            ],*/
        ];
    }

    public static function getQuestionId($user_id)
    {
        /* @var $model QuestionReviewEntity */
        $model = self::find()->where(
            [
                'created_by' => $user_id,
                'status'    => [self::STATUS_PROGRESS, self::STATUS_OVERTIME],
            ]
        )->one();

        $question_id = null;

        if (!$model) {
            /* @var $overtime_model QuestionReviewEntity */
            $overtime_model = self::find()->where(
                [
                    'status' => self::STATUS_OVERTIME,
                ]
            )->one();

            if ($overtime_model) {
                $question_id = $model->$overtime_model;
                $overtime_model->created_by = Yii::$app->user->id;
                $overtime_model->status = self::STATUS_PROGRESS;
                $overtime_model->save();
            } else {
                $sql = sprintf("select max(`question_id`) from %s limit 1", self::tableName());
                $command = self::getDb()->createCommand($sql);
                $max_review_question_id = $command->queryScalar();
                $question = QuestionEntity::find()->where(
                    'id>:id AND status=:status',
                    [
                        ':id'     => $max_review_question_id,
                        ':status' => QuestionEntity::STATUS_ORIGINAL,
                    ]
                )->orderBy('id ASC')->one();
                if ($question) {
                    $question_id = $question->id;
                }
            }


        } else {
            if ($model->status == self::STATUS_OVERTIME) {
                $model->status = self::STATUS_PROGRESS;
                $model->save();
            }
            $question_id = $model->question_id;
        }

        return $question_id;
    }

    public static function completeReview($user_id, $question_id)
    {
        /* @var $model QuestionReviewEntity */
        $model = self::find()->where(
            'created_by=:created_by AND question_id=:question_id AND status!=:status',
            [
                ':created_by'   => $user_id,
                ':question_id' => $question_id,
                ':status'      => self::STATUS_COMPLETE,
            ]
        )->one();

        if ($model) {
            $model->status = self::STATUS_COMPLETE;
            if ($model->save()) {
                Counter::addCommonEdit($user_id);
            }

            return true;
        } else {
            return false;
        }
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
    public function getQuestion()
    {
        return $this->hasOne(QuestionEntity::className(), ['id' => 'question_id']);
    }
}
