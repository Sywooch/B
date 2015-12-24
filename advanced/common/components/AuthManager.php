<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/9
 * Time: 20:51
 */

namespace common\components;

use common\config\RedisKey;
use Redis;
use Yii;
use yii\db\Query;

class AuthManager extends \dektrium\rbac\components\DbManager implements \dektrium\rbac\components\ManagerInterface
{
    /*public function init()
    {
      parent::init();
      if (!Yii::$app->user->isGuest) {
        //我们假设用户的角色是存储在身份
        //$this->assign(Yii::$app->user->identity->id, Yii::$app->user->identity->role);
      }
    }*/

    public function getItems($type = null, $excludeItems = [])
    {
        // you should implement this method or extend your class from \dektrium\rbac\components\DbManager

        Yii::trace(__FUNCTION__, 'rbac');

        $redis_key = [
            RedisKey::REDIS_KEY_RBAC,
            implode(
                ':',
                ['Items', $type, implode(':', $excludeItems)]
            ),
        ];
        $cache_data = Yii::$app->redis->get($redis_key);

        if ($cache_data === false) {
            $cache_data = parent::getItems($type, $excludeItems);
            Yii::$app->redis->set($redis_key, $cache_data);
        }

        return $cache_data;

        return;
    }

    public function getItem($name)
    {
        // you should implement this method or extend your class from \dektrium\rbac\components\DbManager

        Yii::trace(__FUNCTION__, 'rbac');

        $redis_key = [
            RedisKey::REDIS_KEY_RBAC,
            implode(
                ':',
                ['Item', $name]
            ),
        ];
        $cache_data = Yii::$app->redis->get($redis_key);

        if ($cache_data === false) {
            $cache_data = parent::getItem($name);
            Yii::$app->redis->set($redis_key, $cache_data);
        }

        return $cache_data;
    }

    public function getItemsByUser($userId)
    {
        Yii::trace(__FUNCTION__, 'rbac');

        $redis_key = [
            RedisKey::REDIS_KEY_RBAC,
            implode(
                ':',
                ['UserId', $userId]
            ),
        ];
        $cache_data = Yii::$app->redis->get($redis_key);

        if ($cache_data === false) {
            $cache_data = parent::getItemsByUser($userId);
            Yii::$app->redis->set($redis_key, $cache_data);
        }

        return $cache_data;
    }

    public function getAssignments($userId)
    {
        Yii::trace(__FUNCTION__, 'rbac');

        $redis_key = [
            RedisKey::REDIS_KEY_RBAC,
            implode(
                ':',
                ['Assignment', $userId]
            ),
        ];
        $cache_data = Yii::$app->redis->get($redis_key);

        if ($cache_data === false) {
            $cache_data = parent::getAssignments($userId);
            Yii::$app->redis->set($redis_key, $cache_data);
        }

        return $cache_data;

    }

    public function checkAccess($userId, $permissionName, $params = [])
    {
        Yii::trace(__FUNCTION__, 'rbac');

        if (!empty($params)) {
            return parent::checkAccess($userId, $permissionName, $params);
        }

        $redis_key = [
            RedisKey::REDIS_KEY_RBAC,
            implode(
                ':',
                [
                    'CheckAccess',
                    $userId,
                    $permissionName,
                ]
            ),
        ];

        $cache_data = Yii::$app->redis->get($redis_key);

        if ($cache_data === false) {
            $cache_data = parent::checkAccess($userId, $permissionName, $params);
            Yii::$app->redis->set($redis_key, $cache_data);
        }

        return $cache_data;
    }

    public function checkAccessRecursive($user, $itemName, $params, $assignments)
    {
        Yii::trace(__FUNCTION__, 'rbac');


        if (($item = $this->getItem($itemName)) === null) {
            return false;
        }

        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }

        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }

        #开始缓存
        $redis_key = [
            RedisKey::REDIS_KEY_RBAC,
            implode(
                ':',
                [
                    'AccessRecursive',
                    $itemName,
                ]
            ),
        ];


        $parents = Yii::$app->redis->get($redis_key);

        if ($parents === false) {
            $query = new Query;
            $parents = $query->select(['parent'])->from($this->itemChildTable)->where(['child' => $itemName])->column(
                $this->db
            );

            Yii::$app->redis->set($redis_key, $parents);
        }
        #结束缓存

        foreach ($parents as $parent) {
            if ($this->checkAccessRecursive($user, $parent, $params, $assignments)) {
                return true;
            }
        }

        return false;
    }

    public function deleteAllCache()
    {
        $redis_keys = Yii::$app->redis->keys([RedisKey::REDIS_KEY_RBAC, '*']);

        if ($redis_keys) {
            $keys = [];
            foreach ($redis_keys as $redis_key) {
                if (strpos($redis_key, Yii::$app->redis->prefix) === 0) {
                    $keys[] = substr(
                        $redis_key,
                        strlen(
                            implode(
                                ':',
                                [
                                    Yii::$app->redis->prefix,
                                    RedisKey::REDIS_KEY_RBAC,
                                ]
                            )
                        ) + 1
                    );
                }
            }

            //print_r([[RedisKey::REDIS_KEY_RBAC, $keys]]);exit;
            return call_user_func_array([Yii::$app->redis, 'delete'], [[RedisKey::REDIS_KEY_RBAC, $keys]]);
        }
    }
}
