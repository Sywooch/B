<?php

namespace frontend\controllers;

use common\components\Error;
use common\components\Updater;
use common\controllers\BaseController;
use common\entities\NotificationEntity;
use common\entities\UserEntity;
use common\helpers\TimeHelper;
use common\modules\user\models\User;
use Yii;
use yii\data\Pagination;

class NotificationController extends BaseController
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

        #
        Updater::clearNotifyCount(Yii::$app->user->id);

        return $this->render(
            'index',
            [
                'data'  => $data,
                'pages' => $pages,
            ]
        );
    }

    /**
     * 返回通知条数
     * @return mixed
     */
    public function actionGetNotificationCount()
    {
        $user = UserEntity::getUserById(Yii::$app->user->id);

        return $user['notification_count'];
    }


    public function actionReadAll()
    {
        $data = NotificationEntity::updateAll(
            [
                'status'  => NotificationEntity::STATUS_READ,
                'read_at' => TimeHelper::getCurrentTime(),
            ],
            [
                'user_id' => Yii::$app->user->id,
                'status'  => NotificationEntity::STATUS_UNREAD,
            ]
        );

        $result = Error::get($data);

        return $this->jsonOut($result);
    }

    public function actionReadOne($id)
    {
        $data = NotificationEntity::updateAll(
            [
                'status'  => NotificationEntity::STATUS_READ,
                'read_at' => TimeHelper::getCurrentTime(),
            ],
            [
                'id'      => $id,
                'user_id' => Yii::$app->user->id,
                'status'  => NotificationEntity::STATUS_UNREAD,
            ]
        );

        $result = Error::get($data);

        return $this->jsonOut($result);

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
