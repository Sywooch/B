<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "follow".
 *
 * @property string $associate_type
 * @property string $user_id
 * @property integer $associate_id
 * @property string $count_follow
 * @property string $created_at
 * @property string $updated_at
 */
class Follow extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'follow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['associate_type', 'user_id', 'associate_id'], 'required'],
            [['user_id', 'associate_id', 'count_follow', 'created_at', 'updated_at'], 'integer'],
            [['associate_type'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'associate_type' => '类型',
            'user_id' => 'User ID',
            'associate_id' => '关联的对象ID',
            'count_follow' => '关注次数',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
