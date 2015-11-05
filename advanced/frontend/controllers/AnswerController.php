<?php

namespace frontend\controllers;

use common\components\Error;
use common\controllers\BaseController;
use Yii;
use common\entities\AnswerEntity;
use yii\data\ActiveDataProvider;
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
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
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

        if ($model->addAnswer(Yii::$app->request->post())) {
            $data = [
                'answer_item' => $this->renderPartial(
                    '/question/_question_answer_item',
                    [
                        'question_id' => $question_id,
                        'data'  => [$model->getAttributes()],
                        'pages' => null,
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
     * Deletes an existing AnswerEntity model.
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
}
