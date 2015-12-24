<?php
/**
 * 主动关注TAG表，当用户提问时添加TAG时，这些TAG将自动关注。
 * 这些TAG只是用户主动关注的TAG，并不表示用户擅长这些，擅长TAG查看 FollowTagPassiveEntity
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 18:38
 */

namespace common\entities;

use common\behaviors\FollowTagBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\FollowTag;
use Yii;
use yii\db\ActiveRecord;

class FollowTagEntity extends FollowTag
{
    const MAX_FOLLOW_TAG_NUMBER = 5000;

    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            'follow_tag_behavior' => [
                'class' => FollowTagBehavior::className(),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(TagEntity::className(), ['id' => 'tag_id']);
    }
}
