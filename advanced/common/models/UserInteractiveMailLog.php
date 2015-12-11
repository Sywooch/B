<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_interactive_mail_log".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $email
 * @property string $created_at
 */
class UserInteractiveMailLog extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_interactive_mail_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'email'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['email'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'email' => '邮件地址',
            'created_at' => '创建时间',
        ];
    }
}
