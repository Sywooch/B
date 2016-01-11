<?php
namespace common\modules\user\controllers;

use common\components\user\User;
use common\modules\user\models\RegistrationForm;
use common\services\UserService;
use dektrium\user\controllers\RegistrationController as BaseRegistrationController;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class RegistrationController extends BaseRegistrationController
{
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            $this->goHome();
        }

        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException();
        }

        //设置用户当前操作环节
        Yii::$app->user->setStep(User::STEP_REGISTER);

        /** @var RegistrationForm $model */
        $model = Yii::createObject(RegistrationForm::className());

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            $result = UserService::autoLoginByUsername($model->username);
            if ($result) {
                //跳转到欢迎页面
                Yii::$app->user->goWelcome();
            } else {
                $this->goHome();
            }
        }

        return $this->render(
            'register',
            [
                'model'  => $model,
                'module' => $this->module,
            ]
        );
    }
}
