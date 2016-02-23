<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_event_log".
 *
 * @property string $id
 * @property string $user_event_id
 * @property string $associate_type
 * @property string $associate_id
 * @property string $associate_data
 * @property string $created_at
 * @property string $created_by
 */
class UserEventLog extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_event_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_event_id', 'associate_type', 'associate_id'], 'required'],
            [['user_event_id', 'associate_id', 'created_at', 'created_by'], 'integer'],
            [['associate_type'], 'string', 'max' => 45],
            [['associate_data'], 'string', 'max' => 1024]
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
            'associate_type' => '类型:question,answer,answer_comment,article',
            'associate_id' => '关联的对象ID',
            'associate_data' => '关联数据',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
        ];
    }
}
