<?php
namespace frontend\controllers;

use common\controllers\BaseController;
use Yii;
/**
 * default controller
 */
class DefaultController extends BaseController
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }
}
