<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "attachment".
 *
 * @property string $id
 * @property string $associate_type
 * @property integer $associate_id
 * @property string $file_location
 * @property integer $file_size
 * @property string $created_at
 * @property string $created_by
 * @property string $status
 */
class Attachment extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['associate_type', 'status'], 'string'],
            [['associate_id', 'file_location'], 'required'],
            [['associate_id', 'file_size', 'created_at', 'created_by'], 'integer'],
            [['file_location'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'associate_type' => '关联模块',
            'associate_id' => '关联ID',
            'file_location' => '文件位置',
            'file_size' => 'File Size',
            'created_at' => '创建时间',
            'created_by' => '创建用户',
            'status' => '状态',
        ];
    }
}
