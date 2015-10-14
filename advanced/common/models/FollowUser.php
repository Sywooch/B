<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "follow_user".
 *
 * @property integer $user_id
 * @property integer $follow_user_id
 * @property string $create_at
 *
 * @property User $followUser
 * @property User $user
 */
class FollowUser extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'follow_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'follow_user_id'], 'required'],
            [['user_id', 'follow_user_id', 'create_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'follow_user_id' => 'Follow User ID',
            'create_at' => '创建时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowUser()
    {
        return $this->hasOne(User::className(), ['id' => 'follow_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return FollowUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FollowUserQuery(get_called_class());
    }
}
