<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "favorite_data".
 *
 * @property string $id
 * @property string $favorite_id
 * @property string $type
 * @property string $associate_id
 * @property string $create_at
 * @property string $note
 *
 * @property Favorite $favorite
 */
class FavoriteRecord extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorite_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['favorite_id'], 'required'],
            [['favorite_id', 'create_at'], 'integer'],
            [['type'], 'string'],
            [['associate_id', 'note'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'favorite_id' => 'Favorite ID',
            'type' => '类型',
            'associate_id' => '关联的对象ID',
            'create_at' => '创建时间',
            'note' => '注解',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavorite()
    {
        return $this->hasOne(Favorite::className(), ['id' => 'favorite_id']);
    }
}
