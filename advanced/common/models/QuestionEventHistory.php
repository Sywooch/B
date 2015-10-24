<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_event_history".
 *
 * @property string $id
 * @property string $question_id
 * @property integer $event_type
 * @property string $event_content
 * @property string $reason
 * @property string $ip
 * @property string $create_at
 * @property integer $create_by
 * @property string $allow_cancel
 *
 * @property Question $question
 */
class QuestionEventHistory extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_event_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'event_type', 'create_by'], 'required'],
            [['question_id', 'event_type', 'ip', 'create_at', 'create_by'], 'integer'],
            [['event_content', 'allow_cancel'], 'string'],
            [['reason'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => '关联的问题ID',
            'event_type' => '事件类型，创建问题，添加标签……',
            'event_content' => '事件内容',
            'reason' => '为什么进行该操作',
            'ip' => 'IP地址',
            'create_at' => '创建时间',
            'create_by' => '创建用户',
            'allow_cancel' => '是否允许撤消该事件',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * @inheritdoc
     * @return QuestionEventHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QuestionEventHistoryQuery(get_called_class());
    }
}
