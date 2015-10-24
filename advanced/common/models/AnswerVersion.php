<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "answer_version".
 *
 * @property string $id
 * @property string $answer_id
 * @property string $content
 * @property string $reason
 * @property integer $create_by
 * @property string $create_at
 *
 * @property Answer $answer
 */
class AnswerVersion extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['answer_id', 'content', 'create_by'], 'required'],
            [['answer_id', 'create_by', 'create_at'], 'integer'],
            [['content'], 'string'],
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
            'answer_id' => 'Answer ID',
            'content' => 'Content',
            'reason' => 'Reason',
            'create_by' => '创建用户',
            'create_at' => '创建时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answer::className(), ['id' => 'answer_id']);
    }
}
