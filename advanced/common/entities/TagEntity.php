<?php

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TagBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\helpers\ArrayHelper;
use common\models\Tag;
use yii\db\ActiveRecord;
use Yii;

class TagEntity extends Tag
{

    const STATUS_ENABLE = 'enable';
    const STATUS_DISABLE = 'disable';

    public $update_reason;

    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            'tag'       => [
                'class' => TagBehavior::className(),
            ],
        ];
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['update_reason'], 'string', 'max' => 255, 'on' => 'common_edit'];

        return $rules;
    }

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'common_edit' => ['update_reason'],
            ]
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowTags()
    {
        return $this->hasMany(FollowTagEntity::className(), ['follow_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(UserEntity::className(), ['id' => 'user_id'])->viaTable(
            'follow_tag',
            ['follow_tag_id' => 'id']
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowTagPassives()
    {
        return $this->hasMany(FollowTagPassiveEntity::className(), ['follow_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionTags()
    {
        return $this->hasMany(QuestionTagEntity::className(), ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(QuestionEntity::className(), ['id' => 'question_id'])->viaTable(
            'question_tag',
            ['tag_id' => 'id']
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagRelations()
    {
        return $this->hasMany(TagRelationEntity::className(), ['tag_id_1' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagRelations0()
    {
        return $this->hasMany(TagRelationEntity::className(), ['tag_id_2' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagVersions()
    {
        return $this->hasMany(TagVersion::className(), ['tag_id' => 'id']);
    }
}
