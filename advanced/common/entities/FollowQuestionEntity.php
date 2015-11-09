<?php

namespace common\entities;

use common\behaviors\FollowQuestionBehavior;
use common\components\Counter;
use common\components\Error;
use Yii;
use common\exceptions\ParamsInvalidException;
use common\models\FollowQuestion;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
        ];
    }

    /**
     * ��ӹ�ע
     * @param $question_id
     * @param $user_id
     * @return bool
     * @throws ErrorException
     * @throws ParamsInvalidException
     */
    public function addFollow($question_id, $user_id)
    {
        if (empty($user_id) || empty($question_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,question_id']);
        }

        $follow_question_count = UserProfileEntity::find()->select('count_follow')->where(
            ['user_id' => $user_id]
        )->scalar();

        if ($follow_question_count > self::MAX_FOLLOW_NUMBER) {
            return Error::set(Error::TYPE_FOLLOW_QUESTION_FOLLOW_TOO_MUCH_QUESTION, self::MAX_FOLLOW_NUMBER);
        }

        if (!self::findOne(
            [
                'user_id'            => $user_id,
                'follow_question_id' => $question_id,
            ]
        )
        ) {
            $this->create_at = $user_id;
            $this->follow_question_id = $question_id;
            if ($this->save()) {
                Counter::followQuestion($user_id);

                return true;
            } else {
                Yii::error($this->getErrors(), __FUNCTION__);

                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * @param      $question_id
     * @param null $user_id is null, delete all follow
     * @return bool
     * @throws ParamsInvalidException
     * @throws \Exception
     */
    public function removeFollow($question_id, $user_id = null)
    {
        if (empty($question_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,question_id']);
        }

        #delete
        $model = self::find(
            [
                'follow_question_id' => $question_id,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        foreach ($model as $follow_question) {
            if ($follow_question->delete()) {
                Counter::cancelFollowQuestion($follow_question->user_id);
            }
        }

        return true;
    }
    
    public function getFollowUser($question_id)
    {
        if (empty($question_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,question_id']);
        }

        $model = self::find(
            [
                'follow_question_id' => $question_id,
            ]
        )->asArray()->all();

        return $model;
    }

    public function getFollowUserIds($question_id)
    {
        $data = $this->getFollowUser($question_id);

        if ($data) {
            $result = ArrayHelper::getColumn($data, 'id');
        } else {
            $result = [];
        }


        return $result;
    }
}
