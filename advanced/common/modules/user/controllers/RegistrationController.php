<?php
namespace common\modules\user\controllers;

use dektrium\user\controllers\RegistrationController as BaseRegistrationController;
use yii\filters\AccessControl;

class RegistrationController extends BaseRegistrationController
{

    public function actions()
    {
        return [
            'captcha' => [
                'class'         => 'yii\captcha\CaptchaAction',
                //'height' => 50,
                //'width' => 80,
                'minLength'     => 4,
                'maxLength'     => 4,
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
}
