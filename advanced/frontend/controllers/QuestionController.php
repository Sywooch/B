<?php

namespace frontend\controllers;

use common\components\Counter;
use common\components\Error;
use common\controllers\BaseController;
use common\entities\AnswerEntity;
use common\entities\FollowUserEntity;
use common\entities\QuestionEntity;
use common\entities\UserEntity;
use common\helpers\ServerHelper;
use common\models\AnswerQuery;
use dosamigos\qrcode\lib\Encode;
use Yii;
use common\models\Question;
use common\models\QuestionSearch;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuestionController implements the CRUD actions for Question model.
 */
class QuestionController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Question models.
     * @return mixed
     */
    public function actionLatest()
    {
        $pages = new Pagination(
            [
                'totalCount' => QuestionEntity::fetchCount('latest', ServerHelper::checkIsSpider()),
                'pageSize'   => 20,
                'params'     => array_merge($_GET),
            ]
        );

        $data = QuestionEntity::fetchLatest($pages->pageSize, $pages->offset, ServerHelper::checkIsSpider());
        if ($data) {
            $html = $this->renderPartial(
                '/default/question_item_view',
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
                'question_data' => $html,
                'active'        => 'latest',
                'pages'         => $pages,
            ]
        );
    }


    public function actionHot()
    {
        $pages = new Pagination(
            [
                'totalCount' => QuestionEntity::fetchCount('hot', ServerHelper::checkIsSpider()),
                'pageSize'   => 20,
                'params'     => array_merge($_GET),
            ]
        );

        $data = QuestionEntity::fetchHot($pages->pageSize, $pages->offset, ServerHelper::checkIsSpider());

        if ($data) {
            $html = $this->renderPartial(
                '/default/question_item_view',
                [
                    'data'  => $data,
                    'pages' => $pages,
                ]
            );
        } else {
            $html = null;
        }

        return $this->render(
            'index',
            [
                'question_data' => $html,
                'active'        => 'hot',
                'pages'         => $pages,
            ]
        );
    }

    public function actionUnAnswer()
    {
        $pages = new Pagination(
            [
                'totalCount' => QuestionEntity::fetchCount('un-answer', ServerHelper::checkIsSpider()),
                'pageSize'   => 20,
                'params'     => array_merge($_GET),
            ]
        );

        $data = QuestionEntity::fetchUnAnswer($pages->pageSize, $pages->offset, ServerHelper::checkIsSpider());


        if ($data) {
            $html = $this->renderPartial(
                '/default/question_item_view',
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
                'question_data' => $html,
                'active'        => 'un-answer',
                'pages'         => $pages,
            ]
        );
    }

    /**
     * Displays a single Question model.
     * @param string $id
     * @param string $sort
     * @param null   $answer_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id, $sort = 'default', $answer_id = null)
    {

        $question_data = QuestionEntity::getQuestionByQuestionId($id);

        if (ServerHelper::checkIsSpider() && !in_array(
                $question_data['status'],
                explode(',', QuestionEntity::STATUS_DISPLAY_FOR_SPIDER)
            )
        ) {
            throw new NotFoundHttpException();
        }

        $answer_model = new AnswerEntity();

        #增加查看问题计数
        Counter::addQuestionView($id);

        if ($answer_id) {
            $pages = null;
            $answer_data = AnswerEntity::getAnswerListByAnswerId([$answer_id]);
        } else {
            $pages = new Pagination(
                [
                    'totalCount' => AnswerEntity::getAnswerCountByQuestionId($id),
                    'pageSize'   => 20,
                    'params'     => array_merge($_GET, ['#' => 'answer-list']),
                ]
            );
            $answer_data = AnswerEntity::getAnswerListByQuestionId($id, $pages->pageSize, $pages->offset, $sort);
        }

        //print_r($answer_data);exit;

        return $this->render(
            'view',
            [
                'question_data'    => $question_data,
                'answer_model'     => $answer_model,
                'answer_item_html' => $this->renderPartial(
                    '_question_answer_item',
                    [
                        'question_id' => $id,
                        'data'        => $answer_data,
                        'pages'       => $pages,
                    ]
                ),
                'sort'             => $sort,
            ]
        );


    }

    /**
     * Creates a new Question model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new QuestionEntity();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['question/view', 'id' => $model->id]);
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Updates an existing Question model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        /*if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            $model->validate();
            print_r($model->getAttributes());
            exit('dd');
        }*/

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['question/view', 'id' => $model->id]);
        } else {
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing Question model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Question model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Question the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = QuestionEntity::getQuestionByQuestionId($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetAssociateUserIdWhenAnswer($user_id, $question_id = null)
    {
        /* @var $follow_user_entity FollowUserEntity */
        $follow_user_entity = Yii::createObject(FollowUserEntity::className());
        $follow_user_ids = $follow_user_entity->getFollowUserIds($user_id);

        $user_ids = $follow_user_ids;
        if ($question_id) {
            $answer_user_ids = AnswerEntity::getAnswerUserIdsByQuestionId($question_id);
            $user_ids = array_merge($user_ids, $answer_user_ids);
        }

        $user = UserEntity::getUserListByIds($user_ids);

        exit(json_encode($user));
        echo Json::encode($user);
    }
}
