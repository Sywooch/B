<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/30
 * Time: 14:21
 */

namespace common\traits;

use Yii;
use yii\base\Exception;

trait ErrorTrait
{
    public static $message = '操作成功';
    public static $code = 0;
    public static $error;

    public static function set($code, $params = null)
    {
        list($error_category, $error_type) = explode(':', $code);

        if (!$error_category || !$error_type || !isset(self::$error[$error_category]) || !isset(self::$error[$error_category][$error_type])) {
            throw new Exception(sprintf('Undefined Error Key [%s]', $code));
        }

        self::$code = self::$error[$error_category][$error_type][0];

        if ($params) {
            $params = is_array($params) ? $params : [$params];
            self::$message = vsprintf(self::$error[$error_category][$error_type][1], $params);
        } else {
            self::$message = self::$error[$error_category][$error_type][1];
        }

        Yii::error(sprintf('[%d] %s', self::$code, self::$message));

        return false;
    }

    public static function get($data = null)
    {
        return [
            'code'    => self::$code,
            'message' => self::$message,
            'data'    => $data,
        ];
    }

    public static function getMessage()
    {
        return self::$message;
    }

    public static function getCode()
    {
        return self::$code;
    }
}
