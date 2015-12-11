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
 * @property integer $count_hate
 * @property integer $count_like
 * @property integer $count_comment
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $updated_by
 * @property string $reproduce_url
 * @property string $reproduce_username
 * @property string $is_anonymous
 * @property string $is_fold
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
            [['question_id', 'content'], 'required'],
            [['question_id', 'count_hate', 'count_like', 'count_comment', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
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
            'count_hate' => '讨厌数',
            'count_like' => '喜欢数',
            'count_comment' => '评论数',
            'created_at' => '创建时间',
            'created_by' => '用户ID',
            'updated_at' => '修改时间',
            'updated_by' => '修改用户',
            'reproduce_url' => '转载网址',
            'reproduce_username' => '转载谁的',
            'is_anonymous' => '匿名回答',
            'is_fold' => '是否被折叠',
        ];
    }
}
