<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification_type".
 *
 * @property string $id
 * @property string $sign
 * @property string $name
 * @property string $template
 * @property string $required
 */
class NotificationType extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sign', 'template'], 'required'],
            [['sign', 'name'], 'string', 'max' => 45],
            [['template', 'required'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sign' => '通知类型标识，system',
            'name' => '名称',
            'template' => '模板格式<a href=\"%s\" target=\"_blank\">%s</a>回复了你。',
            'required' => '[\"sex\", \"age\"]必填参数',
        ];
    }
}
