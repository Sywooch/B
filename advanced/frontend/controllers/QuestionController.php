<?php

namespace frontend\controllers;

use common\components\Counter;
use common\components\Error;
use common\controllers\BaseController;
use common\entities\AnswerEntity;
use common\entities\FollowUserEntity;
use common\entities\QuestionEntity;
use common\entities\QuestionInviteEntity;
use common\exceptions\ParamsInvalidException;
use common\helpers\ServerHelper;
use common\services\AnswerService;
use common\services\FollowService;
use common\services\QuestionService;
use common\services\UserService;
use Yii;
use common\models\Question;
use common\models\QuestionSearch;
use yii\base\Exception;
use yii\data\Pagination;
use yii\filters\AccessControl;
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
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['create', 'update', 'Invite', 'vote'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['create', 'update', 'Invite', 'vote'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionVote()
    {
        
    }
    
    /**
     * Lists all Question models.
     * @return mixed
     */
    public function actionLatest()
    {
        $pages = new Pagination(
            [
                'totalCount' => QuestionService::fetchCount('latest', ServerHelper::checkIsSpider()),
                'pageSize'   => 20,
                'params'     => array_merge($_GET),
            ]
        );

        $data = QuestionService::fetchLatest($pages->pageSize, $pages->offset, ServerHelper::checkIsSpider());
        if ($data) {
            $html = $this->renderPartial(
                '/default/question_item_view',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = null;
        }
        
        return $this->render(
            'index',
            [
                'question_data' => $html,
                'active'        => 'latest',
                'pages'         => $pages,
            ]
        );
    }
    
    
    public function actionHot()
    {
        $pages = new Pagination(
            [
                'totalCount' => QuestionService::fetchCount('hot', ServerHelper::checkIsSpider()),
                'pageSize'   => 20,
                'params'     => array_merge($_GET),
            ]
        );
        
        $data = QuestionService::fetchHot($pages->pageSize, $pages->offset, ServerHelper::checkIsSpider());
        
        if ($data) {
            $html = $this->renderPartial(
                '/default/question_item_view',
                [
                    'data'  => $data,
                    'pages' => $pages,
                ]
            );
        } else {
            $html = null;
        }
        
        return $this->render(
            'index',
            [
                'question_data' => $html,
                'active'        => 'hot',
                'pages'         => $pages,
            ]
        );
    }
    
    public function actionUnAnswer()
    {
        $pages = new Pagination(
            [
                'totalCount' => QuestionService::fetchCount('un-answer', ServerHelper::checkIsSpider()),
                'pageSize'   => 20,
                'params'     => array_merge($_GET),
            ]
        );
        
        $data = QuestionService::fetchUnAnswer($pages->pageSize, $pages->offset, ServerHelper::checkIsSpider());
        
        
        if ($data) {
            $html = $this->renderPartial(
                '/default/question_item_view',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = null;
        }
        
        return $this->render(
            'index',
            [
                'question_data' => $html,
                'active'        => 'un-answer',
                'pages'         => $pages,
            ]
        );
    }
    
    /**
     * Displays a single Question model.
     * @param string $id
     * @param string $sort
     * @param null   $answer_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id, $sort = 'default', $answer_id = null)
    {
        
        $question_data = QuestionService::getQuestionByQuestionId($id);
        
        if (ServerHelper::checkIsSpider() && !in_array(
                $question_data['status'],
                explode(',', QuestionEntity::STATUS_DISPLAY_FOR_SPIDER)
            )
        ) {
            throw new NotFoundHttpException();
        }
        
        $answer_model = new AnswerEntity();
        
        if ($question_data['tags']) {
            $tags = explode(',', $question_data['tags']);
        } else {
            $tags = [];
        }
        
        $tags = array_merge(QuestionService::getSubjectTags($question_data['subject']), $tags);

        #相似问题
        $similar_question = QuestionService::searchQuestionByTag($tags);
        
        #增加查看问题计数
        Counter::addQuestionView($id);
        
        if ($answer_id) {
            $pages = null;
            $answer_data = AnswerService::getAnswerListByAnswerId([$answer_id]);
        } else {
            //print_r(array_merge($_GET, ['#' => 'answer-list']));exit;
            $pages = new Pagination(
                [
                    'totalCount'      => AnswerService::getAnswerCountByQuestionId($id),
                    'defaultPageSize' => 10,
                    'params'          => array_merge($_GET, ['#' => 'answer-list']),
                    'pageParam'       => 'answer-page',
                    'pageSizeParam'   => 'answer-per-page',
                ]
            );
            
            $answer_data = AnswerService::getAnswerListByQuestionId(
                $id,
                Yii::$app->request->get('answer-page', 1),
                Yii::$app->request->get('answer-per-page', 10),
                $sort
            );
        }
        
        return $this->render(
            'view',
            [
                'question_data'    => $question_data,
                'answer_model'     => $answer_model,
                'answer_item_html' => $this->renderPartial(
                    '_question_answer_item',
                    [
                        'question_id' => $id,
                        'data'        => $answer_data,
                    ]
                ),
                'sort'             => $sort,
                'pages'            => $pages,
                'similar_question' => $similar_question,
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
            return $this->redirect(['question/view', 'id' => $model->id]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['question/view', 'id' => $model->id]);
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
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/
    
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
        $follow_user_ids = FollowService::getFollowUserIds($user_id);
        
        $user_ids = $follow_user_ids;
        if ($question_id) {
            $answer_user_ids = AnswerService::getAnswerUserIdsByQuestionId($question_id);
            $user_ids = array_merge($user_ids, $answer_user_ids);
        }
        
        $user = UserService::getUserListByIds($user_ids);

        return $this->jsonOut($user);
    }
    
    /**
     * @param string $method
     * @throws Exception
     * @throws ParamsInvalidException
     */
    public function actionInvite($method = 'username')
    {
        $allow_method = ['username', 'email'];
        
        if (!in_array($method, $allow_method)) {
            throw new ParamsInvalidException('method');
        }
        
        #POST参数
        $be_invited_user = Yii::$app->request->post('be_invited_user', '');
        $question_id = Yii::$app->request->post('question_id', '');
        
        if (!$be_invited_user || !$question_id) {
            throw new ParamsInvalidException(['be_invited_user', 'question_id']);
        }
        
        switch ($method) {
            case 'username':
                $user_data = UserService::getUserIdByUsername($be_invited_user);
                if ($user_data) {
                    $result = QuestionInviteEntity::inviteToAnswerByNotice(
                        Yii::$app->user->id,
                        $user_data['id'],
                        $question_id
                    );
                } else {
                    Error::set(Error::TYPE_USER_IS_NOT_EXIST);
                }
                
                break;
            
            case 'email':
                $result = QuestionInviteEntity::inviteToAnswerByEmail($question_id, $be_invited_user);
                break;
            default:
                throw new Exception(sprintf('暂未支持 %s 通知', $method));
        }
        
        return $this->jsonOut(Error::get($result));
    }
    
    public function getSimilarQuestionBySubject($subject)
    {
        $similar_question = QuestionService::searchQuestionBySubject($subject);
        
        return $this->jsonOut(Error::get($similar_question));
    }
    
    public function actionExplore()
    {
        
    }
}
