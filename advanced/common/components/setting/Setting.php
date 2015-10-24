<?php
namespace common\components\setting;

use Yii;

/**
 * Class Connection
 * @package common\components\redis
 */
class Setting extends \funson86\setting\Setting
{
    public function get($code)
    {
        $result = Yii::$app->redis->get([REDIS_KEY_SETTING, $code]);
        if (!$result) {
            $result = parent::get($code);
            Yii::$app->redis->set([REDIS_KEY_SETTING, $code], $result);
        }

        return $result;
    }
}

?>
