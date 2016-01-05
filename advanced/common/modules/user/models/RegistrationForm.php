<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/9
 * Time: 20:43
 */

namespace common\modules\user\models;

use common\entities\UserEntity;
use common\services\UserService;
use Yii;

class RegistrationForm extends \dektrium\user\models\RegistrationForm
{
    //public $captcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();


        //长度约束
        $rules['usernameLength'] = [
            'username',
            'string',
            'min' => UserEntity::MIN_USERNAME_LENGTH,
            'max' => UserEntity::MAX_USERNAME_LENGTH,
        ];

        unset($rules['usernamePattern']);

        //$rules[] = ['captcha', 'required'];
        /*$rules[] = [
            'captcha',
            'captcha',
            'captchaAction' => UserService::REGISTER_CAPTCHA_ACTION,
            'message'       => '验证码错误，请重新输入或点击验证码图片重试。',
        ];*/
        //print_r($rules);exit;
        return $rules;
    }

    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        return $attributeLabels[] = ['captcha' => '验证码'];

    }

    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var UserEntity $user */
        $user = Yii::createObject(UserEntity::className());
        $user->setScenario('register');
        $this->loadAttributes($user);

        if (!$user->register()) {
            return false;
        }

        Yii::$app->session->setFlash(
            'info',
            Yii::t(
                'user',
                'Your account has been created and a message with further instructions has been sent to your email'
            )
        );

        return true;
    }
}