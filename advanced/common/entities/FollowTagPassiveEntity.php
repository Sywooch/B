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

use common\components\Error;
use common\helpers\TimeHelper;
use common\models\FollowTagPassive;
use Yii;

class FollowTagPassiveEntity extends FollowTagPassive
{
    const MAX_NUMBER_RECOMMEND_USER = 10;
    const RECENT_PERIOD_OF_TIME = 15; #距最后tag活跃的天数
}
