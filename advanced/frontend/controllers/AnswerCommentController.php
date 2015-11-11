<?php

namespace frontend\controllers;

use common\components\Error;
use common\controllers\BaseController;
use common\entities\AnswerEntity;
use Yii;
use common\entities\AnswerCommentEntity;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
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
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
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
        $answer_model = AnswerEntity::findOne($answer_id);

        $model = new AnswerCommentEntity();
        $model->answer_id = $answer_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $result = Error::get(true);
        } else {
            print_r($model->getErrors());
            $result = Error::get(false);
        }

        return $this->jsonOut($result);
    }

    /**
     * Updates an existing AnswerCommentEntity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
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
    }

    /**
     * Deletes an existing AnswerCommentEntity model.
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
