<?php

namespace frontend\controllers;

use common\components\Error;
use common\controllers\BaseController;
use common\entities\VoteEntity;
use common\exceptions\ModelSaveErrorException;
use common\exceptions\NotFoundModelException;
use common\models\AssociateModel;
use common\models\CacheCommentModel;
use common\services\AnswerService;
use common\services\CommentService;
use common\services\QuestionService;
use common\services\VoteService;
use Yii;
use common\entities\CommentEntity;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommentController implements the CRUD actions for CommentEntity model.
 */
class CommentController extends BaseController
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
                'only'  => ['create', 'update', 'vote'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['create', 'update', 'vote'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CommentEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => CommentEntity::find(),
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
     * Displays a single CommentEntity model.
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

    public function actionCreate($answer_id)
    {
        $model = new CommentEntity();
        $model->associate_type = AssociateModel::TYPE_ANSWER_COMMENT;
        $model->associate_id = $answer_id;

        $answer_data = AnswerService::getAnswerByAnswerId($answer_id);
        $question_data = QuestionService::getQuestionByQuestionId($answer_data['question_id']);

        //print_r(Yii::$app->request->post());exit;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $answer_comment = $model->getAttributes();
            $answer_comment['vote_status'] = false;

            $cache_model = new CacheCommentModel();

            $data = $this->renderPartial(
                '/answer/_answer_comment_item',
                [
                    'question_id'             => $question_data['id'],
                    'answer_id'               => $answer_id,
                    'answer_create_user_id'   => $answer_data['created_by'],
                    'question_create_user_id' => $question_data['created_by'],
                    'data'                    => [$cache_model->build($cache_model->build($answer_comment))],
                ]
            );
            $result = Error::get($data);
        } else {
            throw new ModelSaveErrorException($model);
        }

        return $this->jsonOut($result);
    }

    public function actionUpdate($id, $answer_id, $question_id)
    {
        $question_data = QuestionService::getQuestionByQuestionId($question_id);
        $answer_data = AnswerService::getAnswerByAnswerId($answer_id);

        $model = $this->findModel($id);

        $model->setScenario('common_edit');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(
                [
                    'question/view',
                    'id'         => $question_id,
                    'answer_id'  => $answer_id,
                    'comment_id' => $model->id,
                ]
            );
        } else {
            return $this->render(
                'update',
                [
                    'answer_data'   => $answer_data,
                    'question_data' => $question_data,
                    'model'         => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing CommentEntity model.
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
     * Finds the CommentEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CommentEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CommentEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionVote($id, $vote)
    {
        $vote_status = VoteService::getCommentVoteStatus($id, Yii::$app->user->id);

        if ($vote_status == VoteEntity::VOTE_YES) {
            $result = VoteService::deleteAnswerCommentVote($id, Yii::$app->user->id);
        } else {
            $result = VoteService::addAnswerCommentVote($id, Yii::$app->user->id, $vote);
        }

        $answer_comment = CommentService::getCommentByCommentId($id);

        if ($answer_comment === false) {
            throw new NotFoundModelException('answer_comment', $id);
        }

        if ($result) {
            $vote = ($vote_status == VoteEntity::VOTE_YES) ? false : VoteEntity::VOTE_YES;
        }

        return $this->renderPartial(
            '/answer/_answer_comment_vote',
            [
                'id'          => $id,
                'count_vote'  => $answer_comment['count_like'] - $answer_comment['count_hate'],
                'vote_status' => $vote,
            ]
        );
    }

    public function actionAnonymous($id, $question_id, $answer_id)
    {
        $comment = CommentService::getCommentByCommentId($id);
        if ($comment['is_anonymous'] == CommentEntity::STATUS_ANONYMOUS) {
            CommentService::cancelAnonymous($id);
        } else {
            CommentService::setAnonymous($id);
        }

        $this->redirect(
            [
                'question/view',
                'id'         => $question_id,
                'answer_id'  => $answer_id,
                'comment_id' => $id,
            ]
        );
    }
}
