<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "follow_tag_passive".
 *
 * @property integer $user_id
 * @property string $follow_tag_id
 * @property string $count_follow
 * @property string $created_at
 * @property string $updated_at
 */
class FollowTagPassive extends \common\models\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'follow_tag_passive';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'follow_tag_id'], 'required'],
            [['user_id', 'follow_tag_id', 'count_follow', 'created_at', 'updated_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'follow_tag_id' => '关注的标签ID',
            'count_follow' => '关注次数',
            'created_at' => '第一次关注时间',
            'updated_at' => '最近一次关注时间',
        ];
    }
}
