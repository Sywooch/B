<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_feed".
 *
 * @property string $id
 * @property string $event
 * @property string $associate_type
 * @property string $associate_id
 * @property string $created_at
 * @property string $created_by
 */
class UserFeed extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_feed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event', 'associate_type'], 'required'],
            [['associate_type'], 'string'],
            [['associate_id', 'created_at', 'created_by'], 'integer'],
            [['event'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event' => 'Event',
            'associate_type' => '类型',
            'associate_id' => '关联的对象ID',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
        ];
    }
}
