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
 * @property string $tags
 * @property string $count_views
 * @property string $count_answer
 * @property string $count_favorite
 * @property string $count_follow
 * @property string $create_at
 * @property integer $create_by
 * @property integer $active_at
 * @property string $is_anonymous
 * @property string $is_lock
 * @property string $status
 *
 * @property FollowQuestion[] $followQuestions
 * @property User[] $users
 * @property User $createBy
 * @property QuestionEventHistory[] $questionEventHistories
 * @property QuestionHasTag[] $questionHasTags
 * @property Tag[] $tags0
 * @property QuestionVersion[] $questionVersions
 */
class Question extends \common\models\BaseActiveRecord
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
            [['content', 'is_anonymous', 'is_lock', 'status'], 'string'],
            [['count_views', 'count_answer', 'count_favorite', 'count_follow', 'create_at', 'create_by', 'active_at'], 'integer'],
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
            'tags' => '标签，多个用,分隔，冗余，给sphinx用',
            'count_views' => '查看数',
            'count_answer' => '回答数',
            'count_favorite' => '收藏数',
            'count_follow' => '关注数',
            'create_at' => '创建时间',
            'create_by' => '创建用户',
            'active_at' => '最后活跃时间',
            'is_anonymous' => '是否匿名发表',
            'is_lock' => '是否锁定',
            'status' => 'enable启用 disable禁用 lock 锁定 draft草稿 close关闭',
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
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('follow_question', ['follow_question_id' => 'id']);
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
    public function getQuestionEventHistories()
    {
        return $this->hasMany(QuestionEventHistory::className(), ['associate_id' => 'id']);
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
