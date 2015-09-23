<?php
namespace common\components\redis;

use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * Class Connection
 * @property \Redis $instance
 * @package common\components\redis
 */
class Connection extends Component
{

    public $config, $prefix, $instance_key, $params;
    public static $instance;

    #需要设置过期时间的方法，此方法有待完善
    public $redisSetCommands = [
            'SET',
            'HSET',
            'LSET',
    ];

    public function __destruct()
    {
        unset($this->config, $this->prefix, self::$instance, $this->params);
    }


    /*public function init()
    {


    }*/

    /**
     * 修饰缓存KEY
     * @param $key
     * @param null $identifier
     * @return string
     */
    public function buildCallParams($key, $identifier = null)
    {
        if ($this->prefix) {
            $this->params[0] = implode(':', [$this->prefix, $key]);
        }

        if ($identifier) {
            $this->params[0] = implode(':', [$this->params[0], $identifier]);
        }
    }

    /**
     * 设置过期时间
     * @param $action
     * @param null $expire
     */
    public function buildCackeKeyExpire($action, $expire = null)
    {
        if ($expire && in_array(strtoupper($action), $this->redisSetCommands)) {
            self::$instance[$this->instance_key]->setTimeout($this->params[0], $expire);
        }
    }

    public function getCacheConfig($key)
    {
        if (!isset($this->config[$key]['server'])) {
            throw new Exception("当前key：{$key} 的server项未配置。");
        }

        if (!isset($this->config[$key]['key'])) {
            throw new Exception("当前key：{$key} 的key项未配置。");
        }

        if (!isset($this->config[$key]['expire'])) {
            throw new Exception("当前key：{$key} 的expire项未配置。");
        }

        #添加缓存名称
        $this->config['name'] = $key;

        return $this->config[$key];
    }

    public function createInstance($config)
    {

        if (empty($config['server']['hostname']) || empty($config['server']['port'])) {
            throw new Exception("找不到key {$config['name']}的redis的配置信息!");
        }

        #创建instance key
        $this->instance_key = md5($config['server']['hostname'] . ':' . $config['server']['port']);

        if (!isset(self::$instance[$this->instance_key])) {

            $redis = new \Redis();
            if (!$redis->connect($config['server']['hostname'], $config['server']['port'], 1)) {
                throw new Exception('redis实例连接失败!');
            }

            if (!empty($config['server']['auth'])) {
                $redis->auth($config['server']['auth']);
            }

            if (!empty($config['server']['database'])) {
                $redis->select($config['server']['database']);
            }

            #自动序列化，用igbinary会节约很多内存
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);

            self::$instance[$this->instance_key] = $redis;
        }
    }

    /**
     * 调用redis方法
     * @param string $action
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function __call($action, $params)
    {
        if (!isset($params[0])) {
            throw new Exception('redis参数1不得为空，且为数组格式!');
        }

        #判断第一个参数是否为数组格式，数组格式则为 prefix:array[0]:array[1]，否则为 prefix:array
        if (is_array($params[0])) {
            list($key, $identifier) = $params[0];
        } else {
            $key = $params[0];
            $identifier = null;
        }

        $this->params = $params;

        #获取单项缓存配置
        $config = $this->getCacheConfig($key);

        #创建实例
        $this->createInstance($config);

        #建立请求参数
        $this->buildCallParams($config['key'], $identifier);

        #执行动作
        $result = call_user_func_array([self::$instance[$this->instance_key], $action], $this->params);

        #设置缓存时间
        $this->buildCackeKeyExpire($action, $config['expire']);

        return $result;
    }

}

?>