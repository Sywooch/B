<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "judge_event".
 *
 * @property string $id
 * @property string $name
 * @property string $judge_action_id
 * @property string $params
 * @property string $count_agree
 * @property string $count_disagree
 * @property integer $create_by
 * @property string $create_at
 * @property string $status
 *
 * @property JudgeAction $judgeAction
 * @property JudgeLog[] $judgeLogs
 */
class JudgeEvent extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'judge_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'judge_action_id', 'create_by'], 'required'],
            [['judge_action_id', 'count_agree', 'count_disagree', 'create_by', 'create_at'], 'integer'],
            [['status'], 'string'],
            [['name'], 'string', 'max' => 45],
            [['params'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'judge_action_id' => 'Judge Action ID',
            'params' => 'judge action中需要的参数',
            'count_agree' => '同意',
            'count_disagree' => '不同意',
            'create_by' => '创建用户',
            'create_at' => '创建时间',
            'status' => 'handled 已处理，unhandled 未处理',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJudgeAction()
    {
        return $this->hasOne(JudgeAction::className(), ['id' => 'judge_action_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJudgeLogs()
    {
        return $this->hasMany(JudgeLog::className(), ['judge_event_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return JudgeEventQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new JudgeEventQuery(get_called_class());
    }
}
