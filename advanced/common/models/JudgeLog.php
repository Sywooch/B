<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "judge_log".
 *
 * @property string $id
 * @property string $judge_event_id
 * @property string $result
 * @property string $reason
 * @property string $create_at
 * @property integer $create_by
 *
 * @property JudgeEvent $judgeEvent
 */
class JudgeLog extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'judge_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['judge_event_id', 'create_by'], 'required'],
            [['judge_event_id', 'create_at', 'create_by'], 'integer'],
            [['result'], 'string'],
            [['reason'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'judge_event_id' => 'Judge Event ID',
            'result' => '结果',
            'reason' => '原因',
            'create_at' => '创建时间',
            'create_by' => '创建用户',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJudgeEvent()
    {
        return $this->hasOne(JudgeEvent::className(), ['id' => 'judge_event_id']);
    }

    /**
     * @inheritdoc
     * @return JudgeLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new JudgeLogQuery(get_called_class());
    }
}
