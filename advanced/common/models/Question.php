<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question".
 *
 * @property string $id
 * @property string $subject
 * @property string $alias
 * @property string $content
 * @property string $count_views
 * @property string $count_answer
 * @property string $count_favorite
 * @property string $count_follow
 * @property string $create_at
 * @property integer $create_by
 * @property integer $modify_at
 * @property integer $modify_by
 * @property string $tags
 *
 * @property FollowQuestion[] $followQuestions
 * @property User[] $createBies
 * @property User $createBy
 * @property QuestionHasTag[] $questionHasTags
 * @property Tag[] $tags0
 * @property QuestionVersion[] $questionVersions
 */
class Question extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'create_by'], 'required'],
            [['content'], 'string'],
            [['count_views', 'count_answer', 'count_favorite', 'count_follow', 'create_at', 'create_by', 'modify_at', 'modify_by'], 'integer'],
            [['subject', 'alias'], 'string', 'max' => 45],
            [['tags'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => '主题',
            'alias' => '主题别名',
            'content' => '补充',
            'count_views' => '查看数',
            'count_answer' => '回答数',
            'count_favorite' => '收藏数',
            'count_follow' => '关注数',
            'create_at' => 'Create At',
            'create_by' => 'Create By',
            'modify_at' => 'Modify At',
            'modify_by' => '修改用户',
            'tags' => '标签，多个用,分隔，冗余，给sphinx用',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowQuestions()
    {
        return $this->hasMany(FollowQuestion::className(), ['follow_question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBies()
    {
        return $this->hasMany(User::className(), ['id' => 'create_by'])->viaTable('follow_question', ['follow_question_id' => 'id']);
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
    public function getQuestionHasTags()
    {
        return $this->hasMany(QuestionHasTag::className(), ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags0()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable('question_has_tag', ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionVersions()
    {
        return $this->hasMany(QuestionVersion::className(), ['question_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return QuestionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QuestionQuery(get_called_class());
    }
}
