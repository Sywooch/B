<?php
namespace frontend\controllers;

use common\controllers\BaseController;
use common\helpers\ServerHelper;
use common\services\FollowService;
use common\services\QuestionService;
use common\services\TagService;
use common\services\UserService;
use Yii;

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
        //如果是快速搜索
        $keyword = Yii::$app->request->get('keyword');
        if ($keyword) {
            $this->redirect(['search/query', 'keyword' => $keyword]);
        }

        $data = QuestionService::fetchLatest(30, 0, ServerHelper::checkIsSpider());

        if ($data) {
            $html = $this->renderPartial(
                'question_item_view',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = '';
        }


        return $this->render(
            'index',
            [
                'question_latest'  => $html
            ]
        );
    }

    public function actionFetchHottest()
    {
        $data = QuestionService::fetchHot(30, 0, ServerHelper::checkIsSpider(), 30);
        if ($data) {
            $html = $this->renderPartial(
                'question_item_view',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = '';
        }

        $this->htmlOut($html);
    }

    public function actionFetchUnAnswer()
    {
        $data = QuestionService::fetchUnAnswer(30, 0, ServerHelper::checkIsSpider());
        if ($data) {
            $html = $this->renderPartial(
                'question_item_view',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = '';
        }

        $this->htmlOut($html);
    }
}
