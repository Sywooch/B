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
 * @property string $count_follow
 * @property string $type
 * @property string $create_at
 * @property string $create_by
 * @property string $modify_at
 * @property string $modify_by
 * @property string $active_at
 * @property string $status
 *
 * @property FollowTag[] $followTags
 * @property User[] $users
 * @property FollowTagPassive[] $followTagPassives
 * @property User[] $users0
 * @property TagRelation[] $tagRelations
 * @property TagRelation[] $tagRelations0
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
            [['weight', 'count_follow', 'create_at', 'create_by', 'modify_at', 'modify_by', 'active_at'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 45],
            [['type'], 'string', 'max' => 3],
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
            'name' => '名称',
            'alias' => '别名',
            'content' => '描述',
            'weight' => '权重',
            'count_follow' => '关注数',
            'type' => '词性，名词，地名等',
            'create_at' => '创建时间',
            'create_by' => '创建用户',
            'modify_at' => '修改时间',
            'modify_by' => '修改用户',
            'active_at' => '最后活跃时间',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowTags()
    {
        return $this->hasMany(FollowTag::className(), ['follow_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('follow_tag', ['follow_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowTagPassives()
    {
        return $this->hasMany(FollowTagPassive::className(), ['follow_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('follow_tag_passive', ['follow_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagRelations()
    {
        return $this->hasMany(TagRelation::className(), ['tag_id_1' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagRelations0()
    {
        return $this->hasMany(TagRelation::className(), ['tag_id_2' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagVersions()
    {
        return $this->hasMany(TagVersion::className(), ['tag_id' => 'id']);
    }
}
