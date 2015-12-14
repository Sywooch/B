<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vote".
 *
 * @property string $id
 * @property string $type
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
            [['type', 'associate_id'], 'required'],
            [['type', 'vote'], 'string'],
            [['associate_id', 'created_at', 'created_by'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'associate_id' => '关联的对象ID',
            'vote' => '赞成or反对',
            'created_at' => '创建时间',
            'created_by' => 'Created By',
        ];
    }
}
