<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property string $id
 * @property integer $receiver
 * @property string $notice_code
 * @property string $date
 * @property string $associate_type
 * @property integer $associate_id
 * @property string $associate_data
 * @property string $status
 */
class Notification extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['receiver', 'notice_code', 'date', 'associate_type', 'associate_id'], 'required'],
            [['receiver', 'notice_code', 'associate_id'], 'integer'],
            [['date'], 'safe'],
            [['status'], 'string'],
            [['associate_type'], 'string', 'max' => 45],
            [['associate_data'], 'string', 'max' => 255],
            [['receiver', 'notice_code', 'date', 'associate_type', 'associate_id'], 'unique', 'targetAttribute' => ['receiver', 'notice_code', 'date', 'associate_type', 'associate_id'], 'message' => 'The combination of 接收通知的用户ID, 通知类型代码, Date, Associate Type and Associate ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'receiver' => '接收通知的用户ID',
            'notice_code' => '通知类型代码',
            'date' => 'Date',
            'associate_type' => 'Associate Type',
            'associate_id' => 'Associate ID',
            'associate_data' => '关联数据',
            'status' => 'unread未读,  read已读',
        ];
    }
}
