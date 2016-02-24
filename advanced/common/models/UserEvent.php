<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_event".
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property string $event
 * @property string $description
 * @property integer $sort
 * @property string $need_record
 * @property string $event_template
 * @property string $need_notice
 * @property string $notice_template
 * @property string $status
 */
class UserEvent extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group', 'name', 'event'], 'required'],
            [['sort'], 'integer'],
            [['need_record', 'need_notice', 'status'], 'string'],
            [['group', 'name', 'event'], 'string', 'max' => 45],
            [['description', 'event_template', 'notice_template'], 'string', 'max' => 1024],
            [['event'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => '分类组',
            'name' => '事件名称',
            'event' => '事件名称',
            'description' => '描述',
            'sort' => 'Sort',
            'need_record' => '是否需要将该事件记录到 user_event_log表中',
            'event_template' => '事件模板',
            'need_notice' => '是否需要通知',
            'notice_template' => '通知模板',
            'status' => '状态',
        ];
    }
}
