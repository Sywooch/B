<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 14:12
 */

namespace common\modules\user\models;

use common\helpers\AvatarHelper;
use \dektrium\user\models\User as BaseUser;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class User
 * @package common\modules\user\models
 * @property  common\modules\user\models\Profile $profile
 */
class User extends BaseUser
{
    public $avatar;
    #注册用户名正则，允许中英文
    public static $usernameRegexp = '/^[_-a-zA-Z0-9\.\x{4e00}-\x{9fa5}]+$/u';

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
     * @param int $size
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

}