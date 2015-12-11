<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report".
 *
 * @property integer $id
 * @property string $associate_id
 * @property string $report_object
 * @property string $report_reason
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $updated_by
 * @property string $status
 */
class Report extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_object', 'report_reason'], 'required'],
            [['report_object', 'status'], 'string'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['associate_id'], 'string', 'max' => 45],
            [['report_reason'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'associate_id' => '关联的对象ID',
            'report_object' => '举报对象',
            'report_reason' => '举报原因',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
            'updated_at' => '修改时间',
            'updated_by' => '修改用户',
            'status' => 'unprocessed 未处理,confirmed已确认,unconfirmed未确认',
        ];
    }
}
