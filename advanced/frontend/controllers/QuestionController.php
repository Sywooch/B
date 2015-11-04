<?php

namespace frontend\controllers;

use common\components\Counter;
use common\components\Error;
use common\controllers\BaseController;
use common\entities\AnswerEntity;
use common\entities\FollowUserEntity;
use common\entities\QuestionEntity;
use common\entities\UserEntity;
use common\helpers\ServerHelper;
use dosamigos\qrcode\lib\Encode;
use Yii;
use common\models\Question;
use common\models\QuestionSearch;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuestionController implements the CRUD actions for Question model.
 */
class QuestionController extends BaseController
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
     * Lists all Question models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QuestionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index',
            [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Question model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $question_model = $this->findModel($id);

        if (ServerHelper::checkIsSpider() && !in_array(
                $question_model->status,
                explode(',', QuestionEntity::STATUS_DISPLAY_FOR_SPIDER)
            )
        ) {
            throw new NotFoundHttpException();
        }

        $answer_model = new AnswerEntity();

        Counter::addQuestionView($id);

        $answer_model_query = AnswerEntity::find()->where(['question_id' => $id]);

        $pages = new Pagination(
            [
                'totalCount' => $answer_model_query->count(),
                'pageSize'   => 20,
                'params'     => array_merge($_GET, ['#' => 'answer-list']),
            ]
        );

        $answer_data = $answer_model_query->offset($pages->offset)->limit($pages->limit)->asArray()->all();


        return $this->render(
            'view',
            [
                'question_model'   => $question_model,
                'answer_model'     => $answer_model,
                'answer_item_html' => $this->renderPartial(
                    '_question_answer_item',
                    [
                        'data'  => $answer_data,
                        'pages' => $pages,
                    ]
                ),
            ]
        );


    }

    /**
     * Creates a new Question model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new QuestionEntity();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->render(
                '_after_success',
                [
                    'model' => $model,
                ]
            );
        } else {
            return $this->render(
                'create',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Updates an existing Question model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        /*if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            $model->validate();
            print_r($model->getAttributes());
            exit('dd');
        }*/

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->render(
                '_after_success',
                [
                    'model' => $model,
                ]
            );
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
     * Deletes an existing Question model.
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
     * Finds the Question model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Question the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = QuestionEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetAssociateUserIdWhenAnswer($user_id, $question_id = null)
    {
        /* @var $follow_user_entity FollowUserEntity */
        $follow_user_entity = Yii::createObject(FollowUserEntity::className());
        $follow_user_ids = $follow_user_entity->getFollowUserIds($user_id);

        $user_ids = $follow_user_ids;
        if ($question_id) {
            $answer_user_ids = AnswerEntity::getAnswerUserIdsByQuestionId($question_id);
            $user_ids = array_merge($user_ids, $answer_user_ids);
        }
        /* @var $user_entity UserEntity */
        $user_entity = Yii::createObject(UserEntity::className());
        $user = $user_entity->getUserById($user_ids);

        exit(json_encode($user));
        echo Json::encode($user);
    }
}
