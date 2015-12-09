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

use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\FollowTag;
use Yii;
use yii\db\ActiveRecord;

class FollowTagEntity extends FollowTag
{
    public function behaviors()
    {
        return [
            'operator'                 => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'user_id',
                ],
            ],
            'timestamp'                => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_at', 'modify_at'],
                ],
            ],
        ];
    }
}