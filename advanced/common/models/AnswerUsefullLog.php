<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "answer_usefull_log".
 *
 * @property integer $user_id
 * @property string $answer_id
 * @property string $create_at
 *
 * @property User $user
 * @property Answer $answer
 */
class AnswerUsefullLog extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer_usefull_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'answer_id'], 'required'],
            [['user_id', 'answer_id', 'create_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'answer_id' => 'Answer ID',
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
    public function getAnswer()
    {
        return $this->hasOne(Answer::className(), ['id' => 'answer_id']);
    }
}
