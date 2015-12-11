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
 * @property integer $count_like
 * @property integer $count_hate
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $is_anonymous
 * @property string $is_lock
 * @property string $status
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
            [['subject'], 'required'],
            [['content', 'is_anonymous', 'is_lock', 'status'], 'string'],
            [['count_views', 'count_answer', 'count_favorite', 'count_follow', 'count_like', 'count_hate', 'created_at', 'created_by', 'updated_at'], 'integer'],
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
            'count_like' => '喜欢这个话题',
            'count_hate' => '讨厌这个话题',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
            'updated_at' => '最后活跃时间',
            'is_anonymous' => '是否匿名发表',
            'is_lock' => '是否锁定',
            'status' => 'original原稿 review 审稿 edited已编辑 recommend推荐 disable禁用 lock 锁定 crawl抓取',
        ];
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
