<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property string $area_id
 * @property string $parent_id
 * @property string $path
 * @property string $grade
 * @property string $name
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'grade'], 'integer'],
            [['path', 'name'], 'required'],
            [['path'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'area_id' => Yii::t('common', 'Area ID'),
            'parent_id' => Yii::t('common', 'Parent ID'),
            'path' => Yii::t('common', 'Path'),
            'grade' => Yii::t('common', 'Grade'),
            'name' => Yii::t('common', 'Name'),
        ];
    }

    /**
     * @inheritdoc
     * @return AreaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AreaQuery(get_called_class());
    }
}
