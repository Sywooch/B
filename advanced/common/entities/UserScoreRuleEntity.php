<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/5
 * Time: 19:19
 */

namespace common\entities;

use common\behaviors\UserScoreRuleBehavior;
use common\helpers\ArrayHelper;
use common\models\UserScoreRule;

class UserScoreRuleEntity extends UserScoreRule
{
    const STATUS_ENABLE = 'enable';
    const STATUS_DISABLE = 'disable';

    const LIMIT_TYPE_LIMITLESS = 'limitless';
    const LIMIT_TYPE_YEAR = 'year';
    const LIMIT_TYPE_SEASON = 'season';
    const LIMIT_TYPE_MONTH = 'month';
    const LIMIT_TYPE_WEEK = 'week';
    const LIMIT_TYPE_DAY = 'day';
    const LIMIT_TYPE_HOUR = 'hour';
    const LIMIT_TYPE_MINUTE = 'minute';
    const LIMIT_TYPE_SECOND = 'second';

    public function behaviors()
    {
        return [
            'user_event' => [
                'class' => UserScoreRuleBehavior::className(),
            ],
        ];
    }

    public function getUserEventList()
    {
        $model = UserEventEntity::find()->select(['id', 'name'])->where(
            ['status' => UserEventEntity::STATUS_ENABLE]
        )->all();

        return ArrayHelper::map($model, 'id', 'name');
    }

}
