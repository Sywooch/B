<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "judge_action".
 *
 * @property string $id
 * @property string $name
 * @property string $action
 * @property string $required
 *
 * @property JudgeEvent[] $judgeEvents
 */
class JudgeAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'judge_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'action'], 'required'],
            [['name', 'action', 'required'], 'string', 'max' => 45]
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
            'action' => '动作',
            'required' => '必需的参数',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJudgeEvents()
    {
        return $this->hasMany(JudgeEvent::className(), ['judge_action_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return JudgeActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new JudgeActionQuery(get_called_class());
    }
}
