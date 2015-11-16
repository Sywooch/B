<?php

namespace frontend\controllers;

use common\entities\NotificationEntity;
use common\modules\user\models\User;
use yii\data\ActiveDataProvider;
use Yii;
use yii\data\Pagination;

class NotificationController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $count = NotificationEntity::find()->where(['receiver' => Yii::$app->user->id])->count(1);

        $pages = new Pagination(
            [
                'totalCount' => $count,
                'pageSize'   => 50,
                'params'     => array_merge($_GET),
            ]
        );

        if ($count) {
            $data = NotificationEntity::find()->where(['receiver' => Yii::$app->user->id])->limit(
                $pages->limit
            )->offset(
                $pages->offset
            )->orderBy('create_at DESC')->asArray()->all();
        } else {
            $data = [];
        }

        $data = NotificationEntity::makeUpNotification($data);
        //print_r($data);
        //exit;

        //NotificationEntity::clearNotifyCount();

        return $this->render(
            'index',
            [
                'data' => $data,
                'pages' => $pages,
            ]
        );
    }

    /**
     * 返回通知条数
     * @return mixed
     */
    public function actionCount()
    {
        $model = User::findOne(Yii::$app->user->id);

        return $model->notification_count;
    }

    /**
     * 清空通知
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionClearAll()
    {
        NotificationEntity::deleteAll(['user_id' => Yii::$app->user->id]);

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = NotificationEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
