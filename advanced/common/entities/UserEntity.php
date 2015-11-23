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
    #官方账号的最大ID
    const MAX_OFFICIAL_ACCOUNT_ID = 785;
    
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

    /*public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'user_behavior' => [
                'class' => QuestionBehavior::className(),
            ],
        ];
        return $behaviors;
    }*/
    
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
     * @param      $user_id
     * @param int  $size
     * @param bool $auto_make_avatar 是否自动生成
     * @return string
     * @throws \yii\base\Exception
     */
    public static function getAvatar($user_id, $size = 50, $auto_make_avatar = false)
    {
        $user = self::getUserById($user_id);

        if (!empty($user['avatar'])) {
            $avatarPath = Yii::$app->basePath . Yii::$app->params['avatarPath'];
            $avatarCachePath = Yii::$app->basePath . Yii::$app->params['avatarCachePath'];
            
            #创建文件夹
            FileHelper::createDirectory($avatarCachePath);
            
            #头像地址
            $avatar = Yii::$app->params['avatarCacheUrl'] . $size . '_' . $user['avatar'];
            
            if (!file_exists($avatarCachePath . $size . '_' . $user['avatar'])) {
                #不存在小图，则生成一个
                try {
                    $avatar_dir = $avatarCachePath . $size . '_' . dirname($user['avatar']);
                    
                    #print_r($avatar_dir);exit;
                    
                    if (!file_exists($avatar_dir)) {
                        mkdir($avatar_dir, 0777, true);
                    }
                    \yii\imagine\Image::thumbnail(
                        $avatarPath . $user['avatar'],
                        $size,
                        $size
                    )->save($avatarCachePath . $size . '_' . $user['avatar'], ['quality' => 100]);
                    $avatar = Yii::$app->params['avatarCacheUrl'] . $size . '_' . $user['avatar'];
                } catch (\Imagine\Exception\InvalidArgumentException $e) {
                    $avatar = null;
                }
            }
        } elseif ($auto_make_avatar) {
            #头像不存在，则创建随机头像
            $avatar = (new AvatarHelper($user['id'], $size))->getAvater();
        } else {
            #判断为空
            $avatar = null;
        }
        
        return $avatar;
    }
    
    
    public static function getUserById($user_id)
    {
        $data = self::getUserListByIds([$user_id]);

        return $data ? array_shift($data) : false;
    }

    public static function getUserListByIds($user_ids)
    {
        $result = $cache_miss_key = $cache_data = [];
        foreach ($user_ids as $user_id) {
            $cache_key = [REDIS_KEY_USER, $user_id];
            $cache_data = Yii::$app->redis->hGetAll($cache_key);
            if (empty($cache_data)) {
                $cache_miss_key[] = $user_id;
                $result[$user_id] = null;
            } else {
                $result[$user_id] = $cache_data;
            }
        }

        if ($cache_miss_key) {
            $cache_data = self::find()->where(
                [
                    'id' => $cache_miss_key,
                ]
            )->with('profile')->asArray()->all();

            $cache_user_model = new CacheUserModel();
            $username_id_data = [];

            foreach ($cache_data as $item) {
                #filter attributes
                $item = $cache_user_model->filterAttributes($item);
                $user_id = $item['id'];
                $result[$user_id] = $item;
                #cache user
                $cache_key = [REDIS_KEY_USER, $user_id];
                Yii::$app->redis->hMset($cache_key, $item);

                #cache username to userid
                $username_id_data[$item['username']] = $item['id'];
            }

            #cache username id relation data
            if ($username_id_data) {
                Yii::$app->redis->mset([REDIS_KEY_USER_USERNAME_USERID, $username_id_data]);
            }
        }

        return $result;
    }
    
    public static function getUserByUsername($username)
    {
        #use redis
        if (is_array($username)) {
            $multiple = true;
        } else {
            $multiple = false;
            $username = [$username];
        }
        
        $username = array_values(array_unique(array_filter($username)));
        $data = self::getUserByUsernameUseCache($username);

        $combine_data = array_combine($username, $data);
        if ($multiple) {
            $result = $combine_data;
        } else {
            $result = array_shift($combine_data);
        }
        
        return $result;
    }

    /**
     * @param array|string $username
     * @return array|mixed
     */
    public static function getUserIdByUsername($username)
    {
        #use redis
        if (is_array($username)) {
            $multiple = true;
        } else {
            $multiple = false;
            $username = [$username];
        }
        
        $username = array_values(array_unique(array_filter($username)));

        $data = self::getUserIdByUsernameUseCache($username);

        if ($data) {
            $combine_data = array_combine($username, $data);
            if ($multiple) {
                $result = $combine_data;
            } else {
                $result = array_shift($combine_data);
            }
        } else {
            $result = false;
        }

        
        return $result;
    }

    /**
     * @param int $user_id
     * @return mixed
     */
    public static function getUsernameByUserId($user_id)
    {
        $cache_key = [REDIS_KEY_USER, $user_id];
        $username = Yii::$app->redis->hGet($cache_key, 'username');

        if (false === $username) {
            $data = self::getUserById($user_id);
            if ($data) {
                $username = $data['username'];
            }
        }

        return $username;
    }

    public static function ensureUserHasCached($user_id)
    {
        $cache_key = [REDIS_KEY_USER, $user_id];
        if (Yii::$app->redis->hLen($cache_key) == 0) {
           return self::getUserById($user_id);
        }

        return true;
    }

    private static function getUserIdByUsernameUseCache(array $username)
    {
        $cache_hit_data = Yii::$app->redis->mget([REDIS_KEY_USER_USERNAME_USERID, $username]);
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

                #add to redis cache
                Yii::$app->redis->mset([REDIS_KEY_USER_USERNAME_USERID, $cache_miss_data]);

                $cache_hit_data = Yii::$app->redis->paddingMissData(
                    $cache_hit_data,
                    $cache_miss_key,
                    $cache_miss_data
                );
            }
        }

        return $cache_hit_data;
    }

    public static function checkWhetherIsOfficialAccount($user_id)
    {
        return $user_id <= self::MAX_OFFICIAL_ACCOUNT_ID;
    }

    public function getProfile()
    {
        return $this->hasOne(UserProfileEntity::className(), ['user_id' => 'id']);
    }
}