<?php

namespace frontend\controllers;

use common\controllers\BaseController;
use common\entities\TagEntity;
use common\entities\TagSearchEntity;
use common\entities\TagVersionEntity;
use common\exceptions\NotFoundModelException;
use common\services\FollowService;
use common\services\QuestionService;
use common\services\TagService;
use Yii;
use common\models\Tag;
use common\models\TagSearch;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends BaseController
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
                'only'  => ['create', 'follow'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['create', 'follow'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionFollow($id)
    {
        $is_followed = FollowService::checkUseIsFollowedTag($id, Yii::$app->user->id);

        if ($is_followed) {
            $result = FollowService::removeFollowTag($id, Yii::$app->user->id);
        } else {
            $result = FollowService::addFollowTag($id, Yii::$app->user->id);
        }

        if ($result) {
            $is_followed = !$is_followed;
        }

        $tag = TagService::getTagByTagId($id);

        if (!$tag) {
            throw new NotFoundModelException('question', $id);
        }

        return $this->renderPartial(
            '_tag_follow',
            [
                'id'           => $id,
                'count_follow' => $tag['count_follow'],
                'is_followed'  => $is_followed,
            ]
        );
    }

    /**
     * Lists all Tag models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index',
            [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    public function actionSearch()
    {
        $result = [];

        if (isset(Yii::$app->request->queryParams['query'])) {
            $searchModel = new TagSearch();
            $queryParams = [
                'name'   => trim(urldecode(Yii::$app->request->queryParams['query']), ' 　'),
                'status' => 'Y',
            ];

            $dataProvider = $searchModel->search($queryParams);


            #ajax
            if (Yii::$app->request->getIsAjax()) {
                $result = ArrayHelper::getColumn(
                    $dataProvider->getModels(),
                    function ($model) {
                        return $model->getAttributes(['name']);
                    }
                );
            }
        }

        return $this->jsonOut($result);
    }

    public function actionView($id)
    {
        $pages = new Pagination(
            [
                'totalCount' => QuestionService::getQuestionCountByTagId($id),
                'pageSize'   => 20,
                'params'     => array_merge($_GET),
            ]
        );

        $tag = TagService::getTagByTagId($id);

        if (!$tag) {
            throw new NotFoundModelException('tag', $id);
        }

        //该标签下的问题
        $questions = QuestionService::getQuestionListByTagId($id, $pages->page, $pages->pageSize);

        //关联标签
        $tag_relation = TagService::getRelateTag($id);

        $tag_who_good_at_in_30_days = FollowService::getUserWhichIsGoodAtThisTag($id, 10, 30);
        $tag_who_good_at_in_365_days = FollowService::getUserWhichIsGoodAtThisTag($id, 10, 365);

        //是否已关注
        if (Yii::$app->user->isGuest) {
            $is_followed = false;
        } else {
            $is_followed = FollowService::checkUseIsFollowedTag($id, Yii::$app->user->id);
        }


        return $this->render(
            'view',
            [
                'tag'                         => $tag,
                'questions'                   => $questions,
                'pages'                       => $pages,
                'tag_relation'                => $tag_relation,
                'tag_who_good_at_in_30_days'  => $tag_who_good_at_in_30_days,
                'tag_who_good_at_in_365_days' => $tag_who_good_at_in_365_days,
                'is_followed'                 => $is_followed,
            ]
        );
    }

    /**
     * Creates a new Tag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new Tag();

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
    }*/

    /**
     * Updates an existing Tag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->setScenario('common_edit');

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
     * Deletes an existing Tag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the Tag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TagEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionGetRelateTags($tag_id)
    {
        $data = TagService::getRelateTag($tag_id);

        return $this->jsonOut($data);
    }

    public function actionGetHotTags()
    {
        $data = TagService::getHotTag();

        return $this->jsonOut($data);
    }

    public function actionVersionRepository($id)
    {
        $pages = new Pagination(
            [
                'totalCount'      => TagVersionEntity::find()->where(['tag_id' => $id])->count(),
                'defaultPageSize' => 20,
                'params'          => array_merge($_GET, ['#' => '']),
            ]
        );

        $model = TagVersionEntity::find()->where(
            ['tag_id' => $id]
        )->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render(
            'version_repository',
            [
                'model' => $model,
                'pages' => $pages,
            ]
        );
    }
}
