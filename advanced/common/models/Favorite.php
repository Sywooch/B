<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "favorite".
 *
 * @property string $id
 * @property string $favorite_category_id
 * @property string $type
 * @property string $associate_id
 * @property string $create_at
 * @property integer $create_by
 * @property string $note
 *
 * @property User $createBy
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
            [['favorite_category_id', 'create_at', 'create_by'], 'integer'],
            [['type'], 'string'],
            [['create_by'], 'required'],
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
            'favorite_category_id' => '收藏夹分类ID',
            'type' => '类型',
            'associate_id' => '关联的对象ID',
            'create_at' => '创建时间',
            'create_by' => 'Create By',
            'note' => '注解',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBy()
    {
        return $this->hasOne(User::className(), ['id' => 'create_by']);
    }
}
