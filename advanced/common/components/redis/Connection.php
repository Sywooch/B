<?php
namespace common\components\redis;

use common\helpers\ArrayHelper;
use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * 使用 prefix:category:key 的形式组成一个redis key，保存redis action的第一个参数为一个数组,$redis::set([category, key]);
 * 参考：yii\redis\Connection
 * Class Connection
 * @property \Redis $instance
 * @package common\components\redis
 */
class Connection extends Component
{

    private static $instance;
    public $config;#总配置
    public $prefix;#前端
    public $params;#参数

    private $redis_key_config, $cache_category, $cache_key, $instance_key;

    #todo 需要设置过期时间的方法，此方法有待完善
    public $need_set_expire_command = [
        'SET',
        'SADD',
        'HSET',
        'LSET',
    ];
    #TODO 需要特殊对待的命令
    public $need_special_handling_command = [
        'MGET',
    ];

    public function __destruct()
    {
        unset($this->config, $this->prefix, $this->params);
        self::$instance = null;
    }


    /*public function init()
    {


    }*/

    /**
     * 修饰缓存KEY
     * @return string
     */
    private function buildCallParams()
    {
        #例如　mget(),第一个参数为数组
        if (is_array($this->cache_key)) {
            $data = [];
            if (ArrayHelper::isPureAssociative($this->cache_key)) {
                #关联数组，即 mset 之类的方法，第一个参数　['a'=> 1, 'b'=> 2]
                foreach ($this->cache_key as $key => $value) {
                    if ($this->prefix) {
                        $cache_prefix = implode(':', [$this->prefix, $this->cache_category]);
                    } else {
                        $cache_prefix = $this->cache_category;
                    }
                    $cache_key = implode(':', [$cache_prefix, $key]);
                    $data[$cache_key] = $value;

                }
            } else {
                #非关联数组，即 mget 之类的方法，第一个参数　['a','b']
                foreach ($this->cache_key as $value) {
                    if ($this->prefix) {
                        $cache_prefix = implode(':', [$this->prefix, $this->cache_category]);
                    } else {
                        $cache_prefix = $this->cache_category;
                    }
                    $cache_key = implode(':', [$cache_prefix, $value]);
                    $data[] = $cache_key;
                }
            }

            $this->params[0] = $data;
        } else {
            #非数组
            if ($this->prefix) {
                $cache_prefix = implode(':', [$this->prefix, $this->cache_category]);
            } else {
                $cache_prefix = $this->cache_category;
            }
            $this->params[0] = implode(':', [$cache_prefix, $this->cache_key]);
        }
    }

    /**
     * 设置过期时间
     * @param $action
     */
    public function buildCacheKeyExpire($action)
    {
        if ($this->redis_key_config['expire'] && in_array(strtoupper($action), $this->need_set_expire_command)) {
            self::$instance[$this->instance_key]->setTimeout($this->params[0], $this->redis_key_config['expire']);
        }
    }

    private function getKeyConfig()
    {
        if (!isset($this->config[$this->cache_category]['server'])) {
            throw new Exception("当前key：{$this->cache_category} 的server项未配置。");
        }

        if (!isset($this->config[$this->cache_category]['expire'])) {
            throw new Exception("当前key：{$this->cache_category} 的expire项未配置。");
        }

        #添加缓存名称
        $this->config['name'] = $this->cache_category;

        return $this->config[$this->cache_category];
    }

    public function createInstance()
    {
        if (empty($this->redis_key_config['server']['hostname']) || empty($this->redis_key_config['server']['port'])) {
            throw new Exception("找不到key {$this->redis_key_config['name']}的redis的配置信息!");
        }

        #创建instance key
        $this->instance_key = md5(
            $this->redis_key_config['server']['hostname'] . ':' . $this->redis_key_config['server']['port']
        );


        if (self::$instance[$this->instance_key] === null) {

            $redis = new \Redis();
            if (!$redis->connect(
                $this->redis_key_config['server']['hostname'],
                $this->redis_key_config['server']['port'],
                1
            )
            ) {
                if (YII_DEBUG) {
                    throw new Exception(
                        sprintf(
                            'redis实例[%s:%d]连接失败!',
                            $this->redis_key_config['server']['hostname'],
                            $this->redis_key_config['server']['port']
                        )
                    );
                } else {
                    self::$instance[$this->instance_key] = false;

                    Yii::error(
                        sprintf(
                            'redis实例[%s:%d]连接失败!',
                            $this->redis_key_config['server']['hostname'],
                            $this->redis_key_config['server']['port']
                        ),
                        'redis'
                    );

                    return false;
                }
            }

            if (!empty($this->redis_key_config['server']['auth'])) {
                $redis->auth($this->redis_key_config['server']['auth']);
            }

            if (!empty($this->redis_key_config['server']['database'])) {
                $redis->select($this->redis_key_config['server']['database']);
            }

            #自动序列化，用 igbinary 会节约很多内存
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);

            self::$instance[$this->instance_key] = $redis;
        }

        return true;
    }

    public function buildRedisConfig($params)
    {
        #判断第一个参数是否为数组格式，数组格式则为 prefix:array[0]:array[1]，否则为 prefix:array
        if (empty($params[0]) || !is_array($params[0])) {
            throw new Exception('redis 参数1不得为空，数组格式:[category, id]');
        } elseif (count($params[0]) == 1) {
            $params[0] = array_merge($params[0], ['']);
        }

        list($this->cache_category, $this->cache_key) = $params[0];

        $this->params = $params;

        #获取单项缓存配置
        $this->redis_key_config = $this->getKeyConfig();
    }

    /**
     * 调用redis方法
     * @param string $action
     * @param array  $params
     * @return mixed
     * @throws Exception
     */
    public function __call($action, $params)
    {
        Yii::trace(sprintf('---------- Begin Reids Call: %s, Params in below', $action), 'redis');
        Yii::trace($params, 'redis');

        $this->buildRedisConfig($params);


        #创建实例
        if ($this->createInstance()) {
            #建立请求参数
            $this->buildCallParams();

            #执行动作
            $result = call_user_func_array([self::$instance[$this->instance_key], $action], $this->params);

            #设置缓存时间
            $this->buildCacheKeyExpire($action);

            Yii::trace(sprintf('Reids Call Result:'), 'redis');
            Yii::trace($result, 'redis');
            Yii::trace('++++++++++ End Redis Call', 'redis');

            return $result;
        } else {
            return null;
        }
    }


    /**
     * 返回未命中的key，根据redis返回的数据，如果===false，则为未命中
     * @param array       $query_key      请求的key数组
     * @param array|false $cache_hit_data redis返回的数据
     * @return array
     */
    public function getMissKey(array $query_key, $cache_hit_data)
    {
        if (!$query_key) {
            return [];
        }

        $miss_key = [];

        if ($cache_hit_data) {
            foreach ($cache_hit_data as $key => $item) {
                if (false === $item) {
                    $miss_key[$key] = $query_key[$key];
                }
            }
        } else {
            $miss_key = $query_key;
        }


        return $miss_key;
    }

    /**
     * 缓存未命中，通过查询数据库得到数据，填充回数组
     * @param $cache_hit_data  缓存命中的数据
     * @param $cache_miss_key  缓存未命中的key
     * @param $cache_miss_data 缓存未命中，查询数据后得到的数据
     * @return mixed
     */
    public function paddingMissData($cache_hit_data, $cache_miss_key, $cache_miss_data)
    {
        foreach ($cache_miss_key as $key => $id) {
            #如果缓存未命中，并且数据中也不存在，则直接跳过。后续如需报错，在这里else修改
            if (isset($cache_miss_data[$id])) {
                $cache_hit_data[$key] = $cache_miss_data[$id];
            }
        }

        return $cache_hit_data;
    }

}

?>