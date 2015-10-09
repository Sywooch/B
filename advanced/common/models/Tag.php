<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property string $id
 * @property string $name
 * @property string $alias
 * @property string $content
 * @property integer $weight
 * @property string $status
 * @property string $count_follow
 *
 * @property QuestionHasTag[] $questionHasTags
 * @property Question[] $questions
 * @property TagVersion[] $tagVersions
 */
class Tag extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['content', 'status'], 'string'],
            [['weight', 'count_follow'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 45],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => '别名',
            'content' => '描述',
            'weight' => '权重',
            'status' => '状态',
            'count_follow' => '关注数',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionHasTags()
    {
        return $this->hasMany(QuestionHasTag::className(), ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['id' => 'question_id'])->viaTable('question_has_tag', ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagVersions()
    {
        return $this->hasMany(TagVersion::className(), ['tag_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return TagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TagQuery(get_called_class());
    }
}
