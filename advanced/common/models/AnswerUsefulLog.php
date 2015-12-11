<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "answer_useful_log".
 *
 * @property integer $user_id
 * @property string $answer_id
 * @property string $created_at
 */
class AnswerUsefulLog extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer_useful_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'answer_id'], 'required'],
            [['user_id', 'answer_id', 'created_at'], 'integer']
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
            'created_at' => '创建时间',
        ];
    }
}
