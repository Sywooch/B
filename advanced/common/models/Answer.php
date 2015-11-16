<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "answer".
 *
 * @property string $id
 * @property string $question_id
 * @property string $type
 * @property string $content
 * @property string $count_useful
 * @property string $count_comment
 * @property string $create_at
 * @property integer $create_by
 * @property string $modify_at
 * @property string $modify_by
 * @property string $reproduce_url
 * @property string $reproduce_username
 * @property string $is_anonymous
 * @property string $is_fold
 *
 * @property Question $question
 * @property User $createBy
 * @property AnswerComment[] $answerComments
 * @property AnswerUsefullLog[] $answerUsefullLogs
 * @property User[] $users
 * @property AnswerVersion[] $answerVersions
 */
class Answer extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'content', 'create_by'], 'required'],
            [['question_id', 'count_useful', 'count_comment', 'create_at', 'create_by', 'modify_at', 'modify_by'], 'integer'],
            [['type', 'content', 'is_anonymous', 'is_fold'], 'string'],
            [['reproduce_url'], 'string', 'max' => 45],
            [['reproduce_username'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => '相关问题ID',
            'type' => 'answer 常规回答,referenced 被引用',
            'content' => '内容',
            'count_useful' => '点赞数',
            'count_comment' => '评论数',
            'create_at' => '创建时间',
            'create_by' => '用户ID',
            'modify_at' => '修改时间',
            'modify_by' => '修改用户',
            'reproduce_url' => '转载网址',
            'reproduce_username' => '转载谁的',
            'is_anonymous' => '匿名发表',
            'is_fold' => '是否被折叠',
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
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBy()
    {
        return $this->hasOne(User::className(), ['id' => 'create_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerComments()
    {
        return $this->hasMany(AnswerComment::className(), ['answer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerUsefullLogs()
    {
        return $this->hasMany(AnswerUsefullLog::className(), ['answer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('answer_usefull_log', ['answer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerVersions()
    {
        return $this->hasMany(AnswerVersion::className(), ['answer_id' => 'id']);
    }
}
