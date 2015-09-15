<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 11:50
 */

namespace common\models\setting;

use Yii;

class Setting extends \funson86\setting\models\Setting
{

    public static function updateAll($attributes, $condition = '', $params = [])
    {
        if (parent::updateAll($attributes, $condition, $params)) {
            #更新缓存
            Yii::$app->redis->set(['setting', $condition['code']], $attributes['value']);
        }

        return true;
    }
}