<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "answer_comment".
 *
 * @property string $id
 * @property string $answer_id
 * @property string $content
 * @property string $count_hate
 * @property string $count_like
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $updated_by
 * @property string $is_anonymous
 * @property string $ip
 * @property string $status
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
            [['answer_id', 'content'], 'required'],
            [['answer_id', 'count_hate', 'count_like', 'created_at', 'created_by', 'updated_at', 'updated_by', 'ip'], 'integer'],
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
            'count_hate' => '讨厌数',
            'count_like' => '喜欢数',
            'created_at' => '创建时间',
            'created_by' => 'Created By',
            'updated_at' => '修改时间',
            'updated_by' => '修改用户',
            'is_anonymous' => '匿名评论',
            'ip' => 'IP地址',
            'status' => '状态',
        ];
    }
}
