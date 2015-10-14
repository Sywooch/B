<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "follow_question".
 *
 * @property integer $user_id
 * @property string $follow_question_id
 * @property string $create_at
 *
 * @property User $user
 * @property Question $followQuestion
 */
class FollowQuestion extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'follow_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'follow_question_id'], 'required'],
            [['user_id', 'follow_question_id', 'create_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'follow_question_id' => 'Follow Question ID',
            'create_at' => '创建时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'follow_question_id']);
    }

    /**
     * @inheritdoc
     * @return FollowQuestionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FollowQuestionQuery(get_called_class());
    }
}
