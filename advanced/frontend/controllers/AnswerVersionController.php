<?php

namespace frontend\controllers;

use common\entities\AnswerVersionEntity;
use common\services\AnswerService;
use yii\data\Pagination;

class AnswerVersionController extends \yii\web\Controller
{
    public function actionIndex($answer_id)
    {
        if(\Yii::$app->user->can($this->action->id)){

        }

        $query = AnswerVersionEntity::find()->where(['answer_id' => $answer_id]);

        $pagination = new Pagination(
            [
                'defaultPageSize' => 20,
                'totalCount'      => $query->count(),
            ]
        );

        $answer_version_model = AnswerService::getAnswerVersionList(
            $answer_id,
            $pagination->limit,
            $pagination->offset
        );

        return $this->render(
            'index',
            [
                'answer_version_model' => $answer_version_model,
                'pagination'           => $pagination,
            ]
        );
    }

    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    protected function findModel($id)
    {
        if (($model = AnswerVersionEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
