<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:22
 */

namespace common\modules\user\controllers;

use common\components\user\User;
use dektrium\user\controllers\SecurityController as BaseSecurityController;
use yii\helpers\Url;
use Yii;

class SecurityController extends BaseSecurityController
{
    public function actionLogin()
    {
        if (strpos(Yii::$app->request->getReferrer(), 'login') === false) {
            Url::remember(Yii::$app->request->getReferrer());
        }

        Yii::$app->user->setStep(User::STEP_LOGIN);

        return parent::actionLogin();
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        if (strpos(Yii::$app->request->getReferrer(), 'logout') === false) {
            Url::remember(Yii::$app->request->getReferrer());
        }

        return $this->goBack();
    }
}
