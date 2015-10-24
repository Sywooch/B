<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "follow_tag".
 *
 * @property integer $user_id
 * @property string $tag_id
 * @property string $count_follow
 * @property string $create_at
 * @property string $modify_at
 *
 * @property User $user
 * @property Tag $tag
 */
class FollowTag extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'follow_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'tag_id'], 'required'],
            [['user_id', 'tag_id', 'count_follow', 'create_at', 'modify_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'tag_id' => 'Tag ID',
            'count_follow' => '关注次数',
            'create_at' => '创建时间',
            'modify_at' => '修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id']);
    }
}
