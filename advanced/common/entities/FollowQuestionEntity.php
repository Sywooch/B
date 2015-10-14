<?php

namespace common\entities;

use common\behaviors\FollowQuestionBehavior;
use Yii;
use common\exceptions\ParamsInvalidException;
use common\models\FollowQuestion;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class FollowQuestionEntity extends FollowQuestion
{

    public function behaviors()
    {
        return [
            'operator' => [
                'class' => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'user_id',
                ],
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],

            'follow_question' => [
                'class' => FollowQuestionBehavior::className()
            ]
        ];
    }

    /**
     * Ìí¼Ó¹Ø×¢
     * @param $user_id
     * @param $question_id
     * @return bool
     * @throws ParamsInvalidException
     */
    public function addFollow($question_id, $user_id)
    {
        if (empty($user_id) || empty($question_id)) {
            throw new ParamsInvalidException(['user_id', 'question_id']);
        }

        if (!self::findOne(
            [
                'user_id' => $user_id,
                'follow_question_id' => $question_id
            ]
        )
        ) {
            $this->create_at = $user_id;
            $this->follow_question_id = $question_id;
            if ($this->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * @param $question_id
     * @param null $user_id is null, delete all follow
     * @return bool
     * @throws ParamsInvalidException
     * @throws \Exception
     */
    public function removeFollow($question_id, $user_id = null)
    {
        if (empty($question_id)) {
            throw new ParamsInvalidException(['user_id', 'question_id']);
        }

        #delete
        $model = self::find(
            [
                'follow_question_id' => $question_id
            ]
        )->filterWhere(['user_id' => $user_id])->one();

        if ($model && $model->delete()) {
            return true;
        } else {
            return false;
        }
    }

    /*public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->create_by = Yii::$app->user->identity->getId();
            $this->create_at = time();
        }

        return true;
    }*/


    /*public function afterSave($insert, $changedAttributes)
    {

        if (parent::afterSave($insert, $changedAttributes)) {
        }

        return true;
    }*/

}
