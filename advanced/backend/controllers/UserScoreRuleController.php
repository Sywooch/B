<?php

namespace backend\controllers;

use Yii;
use common\entities\UserScoreRuleEntity;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserScoreRuleController implements the CRUD actions for UserScoreRuleEntity model.
 */
class UserScoreRuleController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserScoreRuleEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UserScoreRuleEntity::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserScoreRuleEntity model.
     * @param integer $user_event_id
     * @param string $type
     * @return mixed
     */
    public function actionView($user_event_id, $type)
    {
        return $this->render('view', [
            'model' => $this->findModel($user_event_id, $type),
        ]);
    }

    /**
     * Creates a new UserScoreRuleEntity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserScoreRuleEntity();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'user_event_id' => $model->user_event_id, 'type' => $model->type]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserScoreRuleEntity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $user_event_id
     * @param string $type
     * @return mixed
     */
    public function actionUpdate($user_event_id, $type)
    {
        $model = $this->findModel($user_event_id, $type);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'user_event_id' => $model->user_event_id, 'type' => $model->type]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserScoreRuleEntity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $user_event_id
     * @param string $type
     * @return mixed
     */
    public function actionDelete($user_event_id, $type)
    {
        $this->findModel($user_event_id, $type)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserScoreRuleEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $user_event_id
     * @param string $type
     * @return UserScoreRuleEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($user_event_id, $type)
    {
        if (($model = UserScoreRuleEntity::findOne(['user_event_id' => $user_event_id, 'type' => $type])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
