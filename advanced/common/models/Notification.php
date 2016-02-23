<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property string $id
 * @property integer $receiver
 * @property string $user_event_id
 * @property string $date
 * @property string $associate_type
 * @property integer $associate_id
 * @property integer $count_notice
 * @property string $status
 * @property integer $created_at
 * @property integer $updated_at
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
            [['receiver', 'user_event_id', 'date', 'associate_type', 'associate_id'], 'required'],
            [['receiver', 'user_event_id', 'associate_id', 'count_notice', 'created_at', 'updated_at'], 'integer'],
            [['date'], 'safe'],
            [['status'], 'string'],
            [['associate_type'], 'string', 'max' => 45],
            [['receiver', 'user_event_id', 'date', 'associate_type', 'associate_id'], 'unique', 'targetAttribute' => ['receiver', 'user_event_id', 'date', 'associate_type', 'associate_id'], 'message' => 'The combination of 接收通知的用户ID, 用户事件ID, Date, Associate Type and Associate ID has already been taken.']
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
            'user_event_id' => '用户事件ID',
            'date' => 'Date',
            'associate_type' => 'Associate Type',
            'associate_id' => 'Associate ID',
            'count_notice' => '通知次数',
            'status' => 'unread未读,  read已读',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
