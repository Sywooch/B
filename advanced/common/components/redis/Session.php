<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/12
 * Time: 9:46
 */

namespace common\components\redis;

use Yii;
use yii\base\InvalidConfigException;

class Session extends \yii\web\Session
{
    private $redis = 'redis';
    public $keyPrefix;

    /**
     * Initializes the redis Session component.
     * This method will initialize the [[redis]] property to make sure it refers to a valid redis connection.
     * @throws InvalidConfigException if [[redis]] is invalid.
     */
    public function init()
    {
        if (is_string($this->redis)) {
            $this->redis = Yii::$app->get($this->redis);
        } elseif (is_array($this->redis)) {
            if (!isset($this->redis['class'])) {
                $this->redis['class'] = Connection::className();
            }
            $this->redis = Yii::createObject($this->redis);
        }
        if (!$this->redis instanceof Connection) {
            throw new InvalidConfigException(
                "Session::redis must be either a Redis connection instance or the application component ID of a Redis connection."
            );
        }
        if ($this->keyPrefix === null) {
            throw new InvalidConfigException(
                "Session::redis keyPrefix is null"
            );
        }
        parent::init();


    }

    /**
     * Returns a value indicating whether to use custom session storage.
     * This method overrides the parent implementation and always returns true.
     * @return boolean whether to use custom storage.
     */
    public function getUseCustomStorage()
    {
        return true;
    }

    /**
     * Session read handler.
     * Do not call this method directly.
     * @param string $id session ID
     * @return string the session data
     */
    public function readSession($id)
    {
        Yii::trace(__METHOD__, 'session');
        $data = $this->redis->get([$this->keyPrefix, $id]);

        return $data === false ? '' : $data;
    }

    /**
     * Session write handler.
     * Do not call this method directly.
     * @param string $id   session ID
     * @param string $data session data
     * @return boolean whether session write is successful
     */
    public function writeSession($id, $data)
    {
        Yii::trace(__METHOD__, 'session');

        return (bool) $this->redis->set([$this->keyPrefix, $id], $data);
    }

    /**
     * Session destroy handler.
     * Do not call this method directly.
     * @param string $id session ID
     * @return boolean whether session is destroyed successfully
     */
    public function destroySession($id)
    {
        Yii::trace(__METHOD__, 'session');

        return (bool) $this->redis->delete([$this->keyPrefix, $id]);
    }
}