<?php

namespace frontend\controllers;

use common\components\Error;
use common\controllers\BaseController;
use common\entities\AnswerCommentEntity;
use common\exceptions\NotFoundModelException;
use common\services\AnswerService;
use common\services\CommentService;
use common\services\QuestionService;
use common\services\VoteService;
use Yii;
use common\entities\AnswerEntity;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AnswerController implements the CRUD actions for AnswerEntity model.
 */
class AnswerController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['create', 'update', 'common-edit', 'vote'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['create', 'update', 'common-edit', 'vote'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all AnswerEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => AnswerEntity::find(),
            ]
        );

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single AnswerEntity model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new AnswerEntity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $question_id
     * @return mixed
     */
    public function actionCreate($question_id)
    {
        $model = new AnswerEntity();
        $model->question_id = $question_id;
        $model->type = AnswerEntity::TYPE_ANSWER;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $data = [
                'answer_item' => $this->renderPartial(
                    '/question/_question_answer_item',
                    [
                        'question_id' => $question_id,
                        'data'        => [$model->getAttributes()],
                        'pages'       => null,
                    ]
                ),
                'answer_form' => $this->renderPartial(
                    '/question/_question_has_answered',
                    [
                        'question_id' => $question_id,
                        'answer_id'   => $model->id,
                    ]
                ),
            ];
            $result = Error::get($data);
        } else {
            $result = Error::get($model->getErrors());
        }

        $this->jsonOut($result);
    }

    /**
     * Updates an existing AnswerEntity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $question_id
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @internal param string $id
     */
    public function actionUpdate($id, $question_id)
    {
        $question_data = QuestionService::getQuestionByQuestionId($question_id);
        $answer_model = $this->findModel($id);

        if ($answer_model->load(Yii::$app->request->post()) && $answer_model->save()) {
            return $this->redirect(['question/view', 'id' => $question_id, 'answer_id' => $id]);
        } else {
            return $this->render(
                'update',
                [
                    'answer_model'  => $answer_model,
                    'question_data' => $question_data,
                ]
            );
        }
    }

    public function actionCommonEdit($id, $question_id)
    {
        $question_data = QuestionService::getQuestionByQuestionId($question_id);
        $answer_model = $this->findModel($id);
        
        //$answer_model->scenario = '';
        #todo 公共编辑状态，有些字段不允许提交
        if ($answer_model->load(Yii::$app->request->post()) && $answer_model->save()) {
            return $this->redirect(['question/view', 'id' => $question_id, 'answer_id' => $id]);
        } else {
            return $this->render(
                'update',
                [
                    'answer_model'  => $answer_model,
                    'question_data' => $question_data,
                ]
            );
        }
    }

    /**
     * Deletes an existing AnswerEntity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the AnswerEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AnswerEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnswerEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetCommentList($id)
    {
        $comment_form = new AnswerCommentEntity();
        $answer_data = AnswerService::getAnswerByAnswerId($id);
        $question_data = QuestionService::getQuestionByQuestionId($answer_data['question_id']);

        $count = CommentService::getCommentCountByAnswerId($id);

        if ($count == 0) {
            $pages = null;
            $comments_data = [];
        } else {
            $pages = new Pagination(
                [
                    'totalCount'    => $count,
                    'pageSize'      => 10,
                    'params'        => array_merge($_GET, ['#' => 'answer-' . $id]),
                    'pageParam'     => 'comment-page',
                    'pageSizeParam' => 'comment-per-page',
                ]
            );
            $comments_data = CommentService::getCommentListByAnswerId(
                $id,
                $pages->limit,
                $pages->offset
            );
        }

        //print_r($comments_data);exit;


        $html = $this->renderAjax(
            '_comment_list',
            [
                'comment_item_html' => $this->renderAjax(
                    '_answer_comment_item',
                    [
                        'answer_id'               => $id,
                        'answer_create_user_id'   => $answer_data['created_by'],
                        'question_create_user_id' => $question_data['created_by'],
                        'data'                    => $comments_data,
                    ]
                ),
                'comment_form'      => $comment_form,
                'answer_data'       => $answer_data,
                'pages'             => $pages,
            ]
        );

        $this->htmlOut($html);
    }

    protected function findAnswer($id)
    {
        if (($answer_data = AnswerService::getAnswerByAnswerId($id)) !== null) {
            return $answer_data;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionVote($id, $vote)
    {
        $vote_status = VoteService::getUseAnswerVoteStatus($id, Yii::$app->user->id);

        if ($vote_status !== false) {
            VoteService::updateAnswerVote(
                $id,
                Yii::$app->user->id,
                $vote
            );
        } else {
            VoteService::addAnswerVote($id, Yii::$app->user->id, $vote);
        }

        $answer = AnswerService::getAnswerByAnswerId($id);

        if ($answer === false) {
            throw new NotFoundModelException('answer', $id);
        }

        return $this->renderPartial(
            '/question/_question_answer_vote',
            [
                'id'          => $id,
                'count_vote'  => $answer['count_like'] - $answer['count_hate'],
                'vote_status' => $vote,
            ]
        );

    }
}
