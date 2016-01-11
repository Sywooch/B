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

class UserGradeRuleBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'eventUserGradeRuleCreate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventUserGradeRuleUpdate',
            ActiveRecord::EVENT_AFTER_DELETE  => 'eventUserGradeRuleDelete',
        ];
    }

    public function eventUserGradeRuleCreate()
    {
        $this->dealWithCache();
    }

    public function eventUserGradeRuleUpdate()
    {
        $this->dealWithCache();
    }

    public function eventUserGradeRuleDelete()
    {
        $this->dealWithCache();
    }

    private function dealWithCache()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $cache_key = [RedisKey::REDIS_KEY_USER_GRADE_RULE];
        Yii::$app->redis->delete($cache_key);
    }
}