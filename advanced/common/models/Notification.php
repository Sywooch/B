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
 * @property string $identifier
 * @property string $associate_data
 * @property string $status
 * @property integer $count_number
 * @property string $created_at
 * @property string $updated_at
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
            [['receiver', 'notice_code', 'date', 'identifier'], 'required'],
            [['receiver', 'notice_code', 'count_number', 'created_at', 'updated_at'], 'integer'],
            [['date'], 'safe'],
            [['status'], 'string'],
            [['identifier'], 'string', 'max' => 32],
            [['associate_data'], 'string', 'max' => 255],
            [['receiver', 'notice_code', 'date', 'identifier'], 'unique', 'targetAttribute' => ['receiver', 'notice_code', 'date', 'identifier'], 'message' => 'The combination of 接收通知的用户ID, 通知类型代码, Date and 标识符，确定某个通知下指定的对象 has already been taken.']
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
            'identifier' => '标识符，确定某个通知下指定的对象',
            'associate_data' => '关联数据',
            'status' => 'unread未读,  read已读',
            'count_number' => '通知数量，用于计算超过多少条后，此类通知不再提醒',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
