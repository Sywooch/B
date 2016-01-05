<?php
namespace common\modules\user\controllers;

use common\components\user\User;
use common\modules\user\models\RegistrationForm;
use dektrium\user\controllers\RegistrationController as BaseRegistrationController;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class RegistrationController extends BaseRegistrationController
{

    public function actions()
    {
        return [
            'captcha' => [
                'class'     => 'yii\captcha\CaptchaAction',
                //'height' => 50,
                //'width' => 80,
                'minLength' => 4,
                'maxLength' => 4,
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => [
                            'captcha',
                            'register',
                            'connect',
                        ],
                        'roles'   => ['?'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => [
                            'confirm',
                            'resend',
                        ],
                        'roles'   => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            $this->goHome();
        }

        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException();
        }

        Yii::$app->user->setStep(User::STEP_REGISTER);

        /** @var RegistrationForm $model */
        $model = Yii::createObject(RegistrationForm::className());

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            return $this->render(
                '/message',
                [
                    'title'  => Yii::t('user', 'Your account has been created'),
                    'module' => $this->module,
                ]
            );
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
