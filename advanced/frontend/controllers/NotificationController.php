<?php

namespace frontend\controllers;

use common\entities\NotificationEntity;
use common\modules\user\models\User;
use yii\data\ActiveDataProvider;

class NotificationController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => NotificationEntity::find()->where(['user_id' => Yii::$app->user->id]),
                'sort'  => [
                    'defaultOrder' => [
                        'created_at' => SORT_DESC,
                        'id'         => SORT_ASC,
                    ],
                ],
            ]
        );
        $notifyCount = NotificationEntity::findNotifyCount();
        NotificationEntity::clearNotifyCount();

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
                'notifyCount'  => $notifyCount,
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
     * Deletes an existing Notification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
