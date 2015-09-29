<?php
/**
 * @Author: forecho
 * @Date:   2015-01-30 23:01:28
 * @Last Modified by:   forecho
 * @Last Modified time: 2015-01-31 21:08:34
 */

namespace common\modules\user\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class AvatarForm extends Model
{
    /** @var string */
    public $avatar;

    /** @return User */
    public function getUser()
    {
        return \Yii::$app->user->identity;
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['avatar'], 'required'],
            [
                ['avatar'],
                'file',
                'extensions' => 'gif, jpg, png',
                'maxSize' => 1024 * 1024 * 2,
                'tooBig' => \Yii::t('app', 'File has to be smaller than 2MB')
            ],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'avatar' => '上传头像',
        ];
    }

    /**
     * Saves new account settings.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $user_profile = $this->user->profile;
            $user_profile->avatar = $this->avatar;
            return $user_profile->save();
        }
        return false;
    }

    /**
     * 获取图片的物理路径
     * fetch stored image file name with complete path
     * @param null $avatar
     * @return string
     */
    public function getImageFile($avatar = null)
    {
        if (!$avatar) {
            $avatar = $this->avatar;
        }

        $avatar = $avatar ? \Yii::$app->basePath . \Yii::$app->params['avatarPath'] . $avatar : null;

        return $avatar;
    }

    /**
     * Process upload of image
     *
     * @return mixed the uploaded image instance
     */
    public function uploadAvatar()
    {
        // get the uploaded file instance. for multiple file uploads
        // the following data will return an array (you may need to use
        // getInstances method)
        $image = UploadedFile::getInstance($this, 'avatar');

        // if no image was uploaded abort the upload
        if (empty($image)) {
            return false;
        }

        // generate a unique file name
        $date_dir = date('Ym');

        $dir = \Yii::$app->basePath . \Yii::$app->params['avatarPath'] . $date_dir;

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->avatar = $date_dir . '/' . \Yii::$app->security->generateRandomString() . ".{$image->extension}";

        // the uploaded image instance
        return $image;
    }

    /**
     * Process deletion of image
     *
     * @return boolean the status of deletion
     */
    public function deleteOldAvatar()
    {
        $file = $this->getImageFile($this->user->profile->avatar);

        // check if file exists on server
        if (empty($file) || !file_exists($file)) {
            return false;
        }
        // 删除头像缩略图
        $avatarCachePath = \Yii::$app->basePath . \Yii::$app->params['avatarCachePath'];
        $files = glob("{$avatarCachePath}/*_{$this->user->profile->avatar}");
        array_walk($files, function ($file) {
            unlink($file);
        });

        // check if uploaded file can be deleted on server
        if (!unlink($file)) {
            return false;
        }


        // if deletion successful, reset your file attributes
        $this->avatar = null;

        return true;
    }
}
