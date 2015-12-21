<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vote".
 *
 * @property string $associate_type
 * @property integer $associate_id
 * @property string $vote
 * @property string $created_at
 * @property string $created_by
 */
class Vote extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['associate_type', 'associate_id', 'created_by'], 'required'],
            [['associate_type', 'vote'], 'string'],
            [['associate_id', 'created_at', 'created_by'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'associate_type' => '关联类型',
            'associate_id' => '关联的对象ID',
            'vote' => '赞成or反对',
            'created_at' => '创建时间',
            'created_by' => 'Created By',
        ];
    }
}
