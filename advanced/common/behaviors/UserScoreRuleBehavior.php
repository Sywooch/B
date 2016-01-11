<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/11
 * Time: 16:15
 */

namespace common\behaviors;

use common\config\RedisKey;
use Yii;
use yii\db\ActiveRecord;

class UserScoreRuleBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'eventUserScoreRuleCreate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventUserScoreRuleUpdate',
            ActiveRecord::EVENT_AFTER_DELETE  => 'eventUserScoreRuleDelete',
        ];
    }

    public function eventUserScoreRuleCreate()
    {
        $this->dealWithCache();
    }

    public function eventUserScoreRuleUpdate()
    {
        $this->dealWithCache();
    }

    public function eventUserScoreRuleDelete()
    {
        $this->dealWithCache();
    }

    private function dealWithCache()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $cache_key = [RedisKey::REDIS_KEY_USER_SCORE_RULE];
        Yii::$app->redis->delete($cache_key);
    }
}