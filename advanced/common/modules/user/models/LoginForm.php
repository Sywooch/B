<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/10
 * Time: 10:24
 */

namespace common\modules\user\models;


use common\entities\UserEntity;
use Yii;

class LoginForm extends \dektrium\user\models\LoginForm
{
    public function loginWithoutPassword()
    {
        $this->user = $this->finder->findUserByUsernameOrEmail($this->login);

        return Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0);
    }
}