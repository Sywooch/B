<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_score_rule".
 *
 * @property integer $user_event_id
 * @property string $type
 * @property integer $score
 * @property string $limit_type
 * @property integer $limit_interval
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
            [['user_event_id', 'score', 'limit_interval'], 'integer'],
            [['type', 'limit_type', 'status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_event_id' => '用户事件ID',
            'type' => '积分变动的类型',
            'score' => 'Score',
            'limit_type' => '限制类型',
            'limit_interval' => '限制间隔时间',
            'status' => '状态',
        ];
    }
}
