<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 14:12
 */

namespace common\entities;

use common\helpers\AvatarHelper;
use common\models\CacheUserModel;
use \dektrium\user\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class User
 * @package \common\modules\user\models
 * @property  \common\entities\UserProfileEntity $profile
 */
class UserEntity extends User
{
    public $avatar;
    #注册用户名正则，允许中英文
    public static $usernameRegexp = '/^[_-a-zA-Z0-9\.\x{4e00}-\x{9fa5}]+$/u';

    public static function tableName()
    {
        return 'user';
    }

    public function attributeLabels()
    {
        $attributes = parent::attributeLabels();

        return ArrayHelper::merge(
            $attributes,
            [
                'last_login_at' => \Yii::t('user', 'Last login at'),
                'login_times'   => \Yii::t('user', 'Login times'),
            ]
        );
    }

    /**
     * 场景约束
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to scenarios
        //$scenarios['create'][]   = 'field';
        //$scenarios['update'][]   = 'field';
        //$scenarios['register'][] = 'field';
        return $scenarios;
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        // add some rules
        //$rules['fieldRequired'] = ['field', 'required'];
        //$rules['usernameLength']=['username', 'string', 'min' => 2, 'max' => 255];
        return $rules;
    }


    /**
     * 获取用户头像
     * @param int  $size
     * @param bool $auto_make_avatar 是否自动生成
     * @return string
     * @throws \yii\base\Exception
     */
    public function getAvatar($size = 50, $auto_make_avatar = false)
    {
        if (isset($this->profile->avatar) && $this->profile->avatar) {
            $avatarPath = Yii::$app->basePath . Yii::$app->params['avatarPath'];
            $avatarCachePath = Yii::$app->basePath . Yii::$app->params['avatarCachePath'];

            #创建文件夹
            FileHelper::createDirectory($avatarCachePath);

            #头像地址
            $avatar = Yii::$app->params['avatarCacheUrl'] . $size . '_' . $this->profile->avatar;

            if (!file_exists($avatarCachePath . $size . '_' . $this->profile->avatar)) {
                #不存在小图，则生成一个
                try {
                    $avatar_dir = $avatarCachePath . $size . '_' . dirname($this->profile->avatar);

                    #print_r($avatar_dir);exit;

                    if (!file_exists($avatar_dir)) {
                        mkdir($avatar_dir, 0777, true);
                    }
                    \yii\imagine\Image::thumbnail(
                        $avatarPath . $this->profile->avatar,
                        $size,
                        $size
                    )->save($avatarCachePath . $size . '_' . $this->profile->avatar, ['quality' => 100]);
                    $avatar = Yii::$app->params['avatarCacheUrl'] . $size . '_' . $this->profile->avatar;
                } catch (\Imagine\Exception\InvalidArgumentException $e) {
                    $avatar = null;
                }
            }
        } elseif ($auto_make_avatar) {
            #头像不存在，则创建随机头像
            $avatar = (new AvatarHelper(\Yii::$app->user->identity->email, $size))->getAvater();
        } else {
            #判断为空
            $avatar = null;
        }

        return $avatar;
    }


    public function getUserById($user_id)
    {
        #use redis
        if (is_array($user_id)) {
            $multiple = true;
        } else {
            $multiple = false;
            $user_id = [$user_id];
        }

        $user_id = array_filter($user_id);
        $data = $this->getUserCacheByUserId($user_id);

        if ($multiple) {
            $result = $data;
        } else {
            $result = array_shift($data);
        }

        return $result;
    }

    public function getUserByUsername($username)
    {
        #use redis
        if (is_array($username)) {
            $multiple = true;
        } else {
            $multiple = false;
            $username = [$username];
        }

        $username = array_filter($username);
        $data = $this->getUserCacheByUsername($username);

        if ($multiple) {
            $result = $data;
        } else {
            $result = array_shift($data);
        }

        return $result;
    }

    public function getUserIdByUsername($username)
    {
        #use redis
        if (is_array($username)) {
            $multiple = true;
        } else {
            $multiple = false;
            $username = [$username];
        }

        $username = array_filter($username);
        $data = $this->getUserIdCacheByUsername($username);

        if ($multiple) {
            $result = $data;
        } else {
            $result = array_shift($data);
        }

        return $result;
    }

    private function getUserCacheByUserId(array $user_id)
    {
        $cache_hit_data = Yii::$app->redis->mget([REDIS_KEY_USER, $user_id]);
        $cache_miss_key = Yii::$app->redis->getMissKey($user_id, $cache_hit_data);

        if (count($cache_miss_key)) {
            $sql = sprintf(
                "SELECT u.id, u.username, u.email, up.nickname, up.sex, up.title, up.bio, up.avatar
                FROM `%s` u
                LEFT JOIN `%s` up
                ON u.id=up.user_id
                WHERE u.id
                IN(%s)",
                UserEntity::tableName(),
                UserProfileEntity::tableName(),
                "'" . implode("','", $cache_miss_key) . "'"
            );

            $model = self::getDb()->createCommand($sql)->queryAll();

            #cache_miss_data 为数组，格式key为索引ID，value为保存到redis中的数据
            $cache_miss_data = [];
            $username_id_data = [];
            foreach ($model as $key => $item) {
                #load useful attributes
                $data = (new CacheUserModel())->filterAttributes($item);
                $cache_miss_data[$item['id']] = $data;
                $username_id_data[$item['username']] = $item['id'];
            }

            if ($cache_miss_data) {
                #cache user miss databases data
                Yii::$app->redis->mset([REDIS_KEY_USER, $cache_miss_data]);

                #cache usename id relation data
                Yii::$app->redis->mset([REDIS_KEY_USERNAME, $username_id_data]);

                #padding miss data
                $cache_hit_data = Yii::$app->redis->paddingMissData(
                    $cache_hit_data,
                    $cache_miss_key,
                    $cache_miss_data
                );
            }
        }

        return $cache_hit_data;
    }

    private function getUserIdCacheByUsername(array $username)
    {
        $cache_hit_data = Yii::$app->redis->mget([REDIS_KEY_USERNAME, $username]);
        $cache_miss_key = Yii::$app->redis->getMissKey($username, $cache_hit_data);

        if (count($cache_miss_key)) {
            $sql = sprintf(
                "SELECT u.id, u.username
                FROM `%s` u
                WHERE u.username
                IN(%s)",
                UserEntity::tableName(),
                "'" . implode("','", $cache_miss_key) . "'"
            );

            $model = self::getDb()->createCommand($sql)->queryAll();

            #cache_miss_data 为数组，格式[index]为索引ID，[value]为保存到redis中的数据
            $cache_miss_data = [];
            foreach ($model as $key => $item) {
                $cache_miss_data[$item['username']] = $item['id'];
            }

            //print_r($cache_miss_data);exit;

            if ($cache_miss_data) {
                $cache_hit_data = Yii::$app->redis->paddingMissData(
                    $cache_hit_data,
                    $cache_miss_key,
                    $cache_miss_data
                );
            }
        }

        return $cache_hit_data;
    }

    private function getUserCacheByUsername(array $username)
    {
        $user_ids = $this->getUserIdCacheByUsername($username);
        if ($user_ids) {
            $result = $this->getUserCacheByUserId($user_ids);
        } else {
            $result = null;
        }

        return $result;
    }

    public function updateUserCache($user_id, $user_data)
    {
        $user_cache_data = Yii::$app->redis->get([REDIS_KEY_USER, $user_id]);
        if ($user_cache_data) {
            $user_data = array_merge($user_cache_data, $user_data);
        }

        $data = (new CacheUserModel())->filterAttributes($user_data);

        return Yii::$app->redis->set([REDIS_KEY_USER, $user_id], $data);
    }
}