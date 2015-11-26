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

use common\components\Counter;
use common\components\Error;
use common\models\FollowTag;
use Yii;

class FollowTagEntity extends FollowTag
{

}