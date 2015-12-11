<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "favorite".
 *
 * @property string $id
 * @property string $favorite_category_id
 * @property string $type
 * @property integer $associate_id
 * @property string $created_at
 * @property string $created_by
 * @property string $note
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
            [['favorite_category_id', 'associate_id', 'created_at', 'created_by'], 'integer'],
            [['type'], 'string'],
            [['associate_id'], 'required'],
            [['note'], 'string', 'max' => 45]
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
            'created_at' => '创建时间',
            'created_by' => 'Created By',
            'note' => '注解',
        ];
    }
}
