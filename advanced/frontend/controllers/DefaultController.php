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
            $html = null;
        }


        //标签
        $tags = TagService::getHotTag(20, 100);

        //关注标签
        if (!Yii::$app->user->isGuest) {
            $user = UserService::getUserById(Yii::$app->user->id);
            $follow_tag_ids = FollowService::getUserFollowTagIds(Yii::$app->user->id);
            $follow_tags = TagService::getTagListByTagIds($follow_tag_ids);
            $follow_tag_count = $user['count_follow_tag'];
        } else {
            $follow_tags = [];
            $follow_tag_count = 0;
        }


        //热门问题
        $question_hottest = QuestionService::fetchHot(15, 0, ServerHelper::checkIsSpider(), 30);

        return $this->render(
            'index',
            [
                'question_latest'  => $html,
                'question_hottest' => $question_hottest,
                'tags'             => $tags,
                'follow_tags'      => $follow_tags,
                'follow_tag_count' => $follow_tag_count,
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
            $html = null;
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
            $html = null;
        }

        $this->htmlOut($html);
    }
}
