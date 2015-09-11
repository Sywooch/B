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
        $result = Yii::$app->redis->get(['setting', $code]);
        if (!$result) {
            $result = parent::get($code);
            Yii::$app->redis->set(['setting', $code], $result);
        }

        return $result;
    }
}

?>
