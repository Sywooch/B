<?php

namespace frontend\controllers;

use common\components\Error;
use common\controllers\BaseController;
use common\entities\AnswerEntity;
use common\services\AnswerService;
use common\services\QuestionService;
use Yii;
use common\entities\AnswerCommentEntity;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AnswerCommentController implements the CRUD actions for AnswerCommentEntity model.
 */
class AnswerCommentController extends BaseController
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
                'only'  => ['create', 'update'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['create', 'update'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all AnswerCommentEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => AnswerCommentEntity::find(),
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
     * Displays a single AnswerCommentEntity model.
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
     * Creates a new AnswerCommentEntity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $answer_id
     * @return mixed
     */
    public function actionCreate($answer_id)
    {
        $model = new AnswerCommentEntity();
        $model->answer_id = $answer_id;

        $answer_data = AnswerService::getAnswerByAnswerId($answer_id);
        $question_data = QuestionService::getQuestionByQuestionId($answer_data['question_id']);

        //print_r(Yii::$app->request->post());exit;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $data = $this->renderPartial(
                '/answer/_answer_comment_item',
                [
                    'answer_id'               => $answer_id,
                    'answer_create_user_id'   => $answer_data['create_by'],
                    'question_create_user_id' => $question_data['create_by'],
                    'data'                    => [$model->getAttributes()],
                ]
            );
            $result = Error::get($data);
        } else {
            Error::set(Error::TYPE_ANSWER_COMMENT_CREATE_FAIL);
            $result = Error::get($model->getErrors());
        }

        return $this->jsonOut($result);
    }

    /**
     * Updates an existing AnswerCommentEntity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }*/

    /**
     * Deletes an existing AnswerCommentEntity model.
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
     * Finds the AnswerCommentEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AnswerCommentEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnswerCommentEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
