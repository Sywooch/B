<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "answer_comment".
 *
 * @property string $id
 * @property string $answer_id
 * @property string $content
 * @property string $create_at
 * @property integer $create_by
 * @property string $modify_at
 * @property integer $modify_by
 * @property string $is_anonymous
 * @property string $ip
 * @property string $status
 *
 * @property Answer $answer
 * @property User $createBy
 */
class AnswerComment extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['answer_id', 'content', 'create_by'], 'required'],
            [['answer_id', 'create_at', 'create_by', 'modify_at', 'modify_by', 'ip'], 'integer'],
            [['content', 'is_anonymous', 'status'], 'string']
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
            'content' => '内容',
            'create_at' => '创建时间',
            'create_by' => 'Create By',
            'modify_at' => '修改时间',
            'modify_by' => '修改用户',
            'is_anonymous' => '是否匿名发表',
            'ip' => 'IP地址',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answer::className(), ['id' => 'answer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBy()
    {
        return $this->hasOne(User::className(), ['id' => 'create_by']);
    }
}
