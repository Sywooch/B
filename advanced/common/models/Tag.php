<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property string $id
 * @property string $name
 * @property string $alias
 * @property string $icon
 * @property string $description
 * @property string $content
 * @property integer $weight
 * @property string $count_follow
 * @property string $count_use
 * @property string $type
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $updated_by
 * @property string $status
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
            [['weight', 'count_follow', 'count_use', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 45],
            [['icon', 'description'], 'string', 'max' => 255],
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
            'icon' => '图标',
            'description' => '描述',
            'content' => '描述',
            'weight' => '权重',
            'count_follow' => '关注数',
            'count_use' => '使用数',
            'type' => '词性，名词，地名等',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
            'updated_at' => '修改时间',
            'updated_by' => '修改用户',
            'status' => '状态',
        ];
    }
}
