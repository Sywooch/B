<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_score_rule".
 *
 * @property integer $user_event_id
 * @property string $type
 * @property integer $score
 * @property string $limit_interval
 * @property integer $limit_times
 * @property string $status
 */
class UserScoreRule extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_score_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_event_id', 'type'], 'required'],
            [['user_event_id', 'score', 'limit_times'], 'integer'],
            [['type', 'limit_interval', 'status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_event_id' => '用户事件ID',
            'type' => '变动类型',
            'score' => '变动值',
            'limit_interval' => '间隔时间',
            'limit_times' => '限制次数',
            'status' => '状态',
        ];
    }
}
