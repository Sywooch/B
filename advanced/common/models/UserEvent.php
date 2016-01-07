<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_event".
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property string $event
 * @property string $description
 * @property integer $sort
 * @property string $record
 * @property string $status
 */
class UserEvent extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group', 'name', 'event'], 'required'],
            [['sort'], 'integer'],
            [['record', 'status'], 'string'],
            [['group', 'name', 'event'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 1024],
            [['event'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => '分类组',
            'name' => '事件名称',
            'event' => '事件名称',
            'description' => '描述',
            'sort' => 'Sort',
            'record' => '是否记录到FEED表',
            'status' => '状态',
        ];
    }
}
