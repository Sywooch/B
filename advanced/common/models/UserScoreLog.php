<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_score_log".
 *
 * @property string $id
 * @property string $user_event_id
 * @property integer $score
 * @property string $type
 * @property string $created_at
 * @property string $created_by
 */
class UserScoreLog extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_score_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_event_id', 'type'], 'required'],
            [['score', 'created_at', 'created_by'], 'integer'],
            [['type'], 'string'],
            [['user_event_id'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_event_id' => 'User Event ID',
            'score' => '积分（声誉度）',
            'type' => '货币',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
        ];
    }
}
