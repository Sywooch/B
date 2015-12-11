<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "follow_question".
 *
 * @property integer $user_id
 * @property string $follow_question_id
 * @property string $created_at
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
            [['user_id', 'follow_question_id', 'created_at'], 'integer']
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
            'created_at' => '创建时间',
        ];
    }
}
