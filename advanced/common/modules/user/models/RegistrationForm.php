<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/9
 * Time: 20:43
 */

namespace common\modules\user\models;


class RegistrationForm extends  \dektrium\user\models\RegistrationForm
{
    public $captcha;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['captcha', 'required'];
        $rules[] = ['captcha', 'captcha'];
        return $rules;
    }
}