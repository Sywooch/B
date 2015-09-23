<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 14:12
 */

namespace common\modules\user\models;

use common\helpers\AvatarHelper;
use \dektrium\user\models\Profile as BaseProfile;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

class Profile extends BaseProfile
{
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    public function attributeLabels()
    {
        $attributes = parent::attributeLabels();
        return ArrayHelper::merge($attributes, [
                'nickname'  => \Yii::t('user', 'Nickname'),
                'title'     => \Yii::t('user', 'Title'),
                'sex'       => \Yii::t('user', 'Sex'),
                'birthday'  => \Yii::t('user', 'Birthday'),
                'avatar'    => \Yii::t('user', 'Avatar'),
                'province'  => \Yii::t('user', 'Province'),
                'city'      => \Yii::t('user', 'City'),
                'district'  => \Yii::t('user', 'District'),
                'address'   => \Yii::t('user', 'Address'),
                'longitude' => \Yii::t('user', 'Longitude'),
                'latitude'  => \Yii::t('user', 'Latitude'),
        ]);
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
        $rules['pcd'] = [['province', 'city', 'district'], 'integer', 'integerOnly' => true];
        $rules['sexIn'] = ['sex', 'in', 'range' => ['男', '女', '保密']];
        $rules['birthday'] = ['birthday', 'date', 'format' => 'php:Y/m/d'];
        $rules['addressLength'] = ['address', 'string', 'max' => '80'];
        $rules['avatarLength'] = ['avatar', 'string', 'max' => '255'];

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
        if (isset($this->avatar) && $this->avatar) {
            $avatarPath = Yii::$app->basePath . Yii::$app->params['avatarPath'];
            $avatarCachePath = Yii::$app->basePath . Yii::$app->params['avatarCachePath'];

            #创建文件夹
            FileHelper::createDirectory($avatarCachePath);

            if (file_exists($avatarCachePath . $size . '_' . $this->avatar)) {
                #头像存在
                $avatar = Yii::$app->params['avatarCacheUrl'] . $size . '_' . $this->avatar;
            } else {
                #不存在小图，则生成一个
                try {
                    \yii\imagine\Image::thumbnail($avatarPath . $this->avatar, $size,
                            $size)->save($avatarCachePath . $size . '_' . $this->avatar, ['quality' => 100]);
                    $avatar = Yii::$app->params['avatarCacheUrl'] . $size . '_' . $this->avatar;
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