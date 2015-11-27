<?php

namespace frontend\controllers;

use common\entities\FavoriteCategoryEntity;
use common\exceptions\PermissionDeniedException;
use common\services\UserService;
use Yii;
use common\entities\FavoriteEntity;
use yii\data\ActiveDataProvider;
use common\controllers\BaseController;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FavoriteController implements the CRUD actions for FavoriteEntity model.
 */
class FavoriteController extends BaseController
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
                'only'  => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all FavoriteEntity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->redirect(['show']);
    }

    public function actionShow($user_id)
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => FavoriteEntity::find(),
            ]
        );

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
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

    public function actionCreateCategory()
    {
        $model = new FavoriteCategoryEntity();
        $model->create_by = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model,
                ]
            );
        }
    }

    public function actionAddFavorite()
    {
        $model = new FavoriteEntity();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model,
                ]
            );
        }
    }

    public function actionUpdateCategory($id)
    {
        $model = $this->findCategoryModel($id);

        if (UserService::checkUserSelf($model->create_by)) {
            throw new PermissionDeniedException();
        }

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

    public function actionDeleteCategory($id)
    {
        $model = $this->findCategoryModel($id);

        if (UserService::checkUserSelf($model->create_by)) {
            throw new PermissionDeniedException();
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteFavorite($id)
    {
        $model = $this->findFavoriteModel($id);

        if (UserService::checkUserSelf($model->create_by)) {
            throw new PermissionDeniedException();
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FavoriteEntity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return FavoriteEntity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findFavoriteModel($id)
    {
        if (($model = FavoriteEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findCategoryModel($id)
    {
        if (($model = FavoriteCategoryEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
