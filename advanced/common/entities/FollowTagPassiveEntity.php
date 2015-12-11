<?php
/**
 * 被动TAG关注表，当用户回答某个问题时，则自动被动关注这个问题的TAG
 * 这些TAG将被计算为用户擅长的TAG
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 18:38
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\FollowTagPassive;
use Yii;
use yii\db\ActiveRecord;

class FollowTagPassiveEntity extends FollowTagPassive
{
    const MAX_NUMBER_RECOMMEND_USER = 10;
    const RECENT_PERIOD_OF_TIME = 15; #距最后tag活跃的天数

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
    public function getFollowTag()
    {
        return $this->hasOne(TagEntity::className(), ['id' => 'follow_tag_id']);
    }
}
