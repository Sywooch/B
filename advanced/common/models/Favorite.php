<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "favorite".
 *
 * @property string $id
 * @property string $name
 * @property string $is_public
 * @property integer $create_by
 * @property integer $active_at
 * @property string $last_favorite_content
 * @property integer $count_follow
 * @property string $count_favorite
 *
 * @property FavoriteRecord[] $favoriteRecords
 */
class Favorite extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'create_by'], 'required'],
            [['is_public'], 'string'],
            [['create_by', 'active_at', 'count_follow', 'count_favorite'], 'integer'],
            [['name', 'last_favorite_content'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '收藏夹名称',
            'is_public' => '是否公开收藏夹',
            'create_by' => '创建用户',
            'active_at' => '最后活动时间',
            'last_favorite_content' => '最后一条收藏内容',
            'count_follow' => '关注收藏夹的人数',
            'count_favorite' => '收藏内容的数量',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteRecords()
    {
        return $this->hasMany(FavoriteRecord::className(), ['favorite_id' => 'id']);
    }
}
