<?php

namespace console\modules\crawler\models;

use Yii;

/**
 * This is the model class for table "crawler".
 *
 * @property string $id
 * @property string $name
 * @property string $sign
 * @property integer $year
 * @property integer $month
 * @property integer $day
 * @property integer $hour
 * @property integer $minute
 * @property integer $second
 * @property integer $next_execute_time
 * @property integer $last_execute_time
 * @property string $status
 */
class Crawler extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crawler';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sign'], 'required'],
            [['year', 'month', 'day', 'hour', 'minute', 'second', 'next_execute_time', 'last_execute_time'], 'integer'],
            [['status'], 'string'],
            [['name', 'sign'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', '名称'),
            'sign' => Yii::t('common', '标识'),
            'year' => Yii::t('common', 'Year'),
            'month' => Yii::t('common', 'Month'),
            'day' => Yii::t('common', 'Day'),
            'hour' => Yii::t('common', 'Hour'),
            'minute' => Yii::t('common', 'Minute'),
            'second' => Yii::t('common', 'Second'),
            'next_execute_time' => Yii::t('common', '下次执行时间'),
            'last_execute_time' => Yii::t('common', '最后一次执行时间'),
            'status' => Yii::t('common', '状态'),
        ];
    }

    /**
     * @inheritdoc
     * @return CrawlerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CrawlerQuery(get_called_class());
    }
}
