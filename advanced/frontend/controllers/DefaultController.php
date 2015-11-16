<?php
namespace frontend\controllers;

use common\controllers\BaseController;
use common\entities\QuestionEntity;
use common\helpers\ServerHelper;
use Yii;
use yii\helpers\Json;

/**
 * default controller
 */
class DefaultController extends BaseController
{
    /**
     * Displays homepage.
     * @return mixed
     */
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $data = QuestionEntity::fetchLatest(30, 0, ServerHelper::checkIsSpider());
        if ($data) {
            $html = $this->renderPartial(
                'question_item_view',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = null;
        }

        return $this->render(
            'index',
            [
                'question_latest' => $html,
            ]
        );
    }

    public function actionFetchHot()
    {
        $data = QuestionEntity::fetchHot(30, 0, ServerHelper::checkIsSpider(), 30);
        if ($data) {
            $html = $this->renderPartial(
                'question_item_view',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = null;
        }

        $this->htmlOut($html);
    }

    public function actionFetchUnAnswer()
    {
        $data = QuestionEntity::fetchUnAnswer(30, 0, ServerHelper::checkIsSpider());
        if ($data) {
            $html = $this->renderPartial(
                'question_item_view',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = null;
        }

        $this->htmlOut($html);
    }
}
