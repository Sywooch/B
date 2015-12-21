<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:22
 */

namespace common\modules\user\controllers;

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

        return parent::actionLogin();
    }

    public function actionLogout()
    {
        Yii::$app->getUser()->logout();
        if (strpos(Yii::$app->request->getReferrer(), 'logout') === false) {
            Url::remember(Yii::$app->request->getReferrer());
        }

        return $this->goBack();
    }
}
