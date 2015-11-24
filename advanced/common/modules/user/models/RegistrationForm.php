<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/9
 * Time: 20:43
 */

namespace common\modules\user\models;


use common\entities\UserEntity;

class RegistrationForm extends \dektrium\user\models\RegistrationForm
{
    public $captcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['captcha', 'required'];
        $rules[] = [
            'captcha',
            'captcha',
            'captchaAction' => UserEntity::CAPTCHA_ACTION,
            'message'       => '验证码错误，请重新输入或点击验证码图片重试。',
        ];

        return $rules;
    }

    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        return $attributeLabels[] = ['captcha' => '验证码'];

    }
}