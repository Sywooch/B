<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_feed".
 *
 * @property string $id
 * @property string $user_event_id
 * @property string $associate_type
 * @property string $associate_id
 * @property string $associate_content
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
            [['user_event_id', 'associate_type', 'associate_id'], 'required'],
            [['user_event_id', 'associate_id', 'created_at', 'created_by'], 'integer'],
            [['associate_type'], 'string'],
            [['associate_content'], 'string', 'max' => 1024]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_event_id' => 'User Event ID',
            'associate_type' => '类型',
            'associate_id' => '关联的对象ID',
            'associate_content' => '关联内容',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
        ];
    }
}
