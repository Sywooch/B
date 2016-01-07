<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use Yii;

class CacheUserScoreRuleModel extends BaseCacheModel
{
    public $name;
    public $user_event_id;
    public $type;
    public $score;
    public $limit_type;
    public $limit_interval;
}
