<?php

namespace frontend\controllers;

use common\components\Error;
use Yii;
use common\entities\ReportEntity;
use yii\data\ActiveDataProvider;
use common\controllers\BaseController;
use yii\filters\AccessControl;
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
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['create'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['create'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new ReportEntity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $object
     * @param $associate_id
     * @return mixed
     */
    public function actionCreate($object, $associate_id)
    {
        $reason = Yii::$app->request->post('reason');

        if(empty($reason)){
            $reason = Yii::$app->request->post('option');
        }

        $model = ReportEntity::find()->where(
            [
                'report_object' => $object,
                'associate_id'  => $associate_id,
                'created_by'    => Yii::$app->user->id,
            ]
        )->one();

        if (!$model) {
            $model = new ReportEntity();
            $model->report_object = $object;
            $model->associate_id = $associate_id;
        }

        $model->report_reason = $reason;

        if ($model->save()) {
            $result = true;
        } else {
            $result = false;
        }

        $this->jsonOut(Error::get($result));
    }
}
