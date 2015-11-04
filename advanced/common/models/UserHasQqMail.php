<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_has_qq_mail".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $email
 * @property string $create_at
 *
 * @property User $user
 */
class UserHasQqMail extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_has_qq_mail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'email'], 'required'],
            [['user_id', 'create_at'], 'integer'],
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
            'create_at' => '创建时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
