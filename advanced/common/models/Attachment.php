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
 * @property integer $create_by
 * @property string $create_at
 * @property string $status
 */
class Attachment extends \yii\db\ActiveRecord
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
            [['associate_id', 'file_location', 'create_by'], 'required'],
            [['associate_id', 'file_size', 'create_by', 'create_at'], 'integer'],
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
            'create_by' => '创建用户',
            'create_at' => '创建时间',
            'status' => '状态',
        ];
    }
}
