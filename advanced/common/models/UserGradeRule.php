<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_grade_rule".
 *
 * @property string $id
 * @property string $name
 * @property integer $credit
 * @property string $status
 */
class UserGradeRule extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_grade_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['credit'], 'integer'],
            [['status'], 'string'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '等级名称',
            'credit' => '积分',
            'status' => '状态',
        ];
    }
}
