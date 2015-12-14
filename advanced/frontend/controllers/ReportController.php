<?php

namespace frontend\controllers;

use common\components\Error;
use Yii;
use common\entities\ReportEntity;
use yii\data\ActiveDataProvider;
use common\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReportController implements the CRUD actions for ReportEntity model.
 */
class ReportController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Creates a new ReportEntity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($object, $associate_id)
    {
        $season = Yii::$app->request->post('season');
        $season = Yii::$app->request->post('option', $season);


        $model = new ReportEntity();
        $model->report_object = $object;
        $model->associate_id = $associate_id;
        $model->created_by = Yii::$app->user->id;
        $model->report_reason = $season;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $result = true;
        } else {
            $result = false;
        }

        $this->jsonOut(Error::get($result));
    }
}
