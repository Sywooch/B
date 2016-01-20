<?php

namespace frontend\controllers;

use common\components\Counter;
use common\components\Error;
use common\controllers\BaseController;
use common\entities\AnswerEntity;
use common\entities\QuestionEntity;
use common\entities\QuestionInviteEntity;
use common\entities\QuestionVersionEntity;
use common\exceptions\NotFoundModelException;
use common\exceptions\ParamsInvalidException;
use common\exceptions\PermissionDeniedException;
use common\helpers\ServerHelper;
use common\services\AnswerService;
use common\services\CommentService;
use common\services\FavoriteService;
use common\services\FollowService;
use common\services\QuestionService;
use common\services\UserService;
use common\services\VoteService;
use Yii;
use common\models\QuestionSearch;
use yii\base\Exception;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Url;
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
                'only'  => ['create', 'update', 'Invite', 'vote', 'follow', 'favorite'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['create', 'update', 'Invite', 'vote', 'follow', 'favorite'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionFollow($question_id)
    {
        $is_followed = FollowService::checkUseIsFollowedQuestion($question_id, Yii::$app->user->id);

        if ($is_followed) {
            $result = FollowService::removeFollowQuestion($question_id, Yii::$app->user->id);
        } else {
            $result = FollowService::addFollowQuestion($question_id, Yii::$app->user->id);
        }

        //操作成功，才能反转是否已关注
        if ($result) {
            $is_followed = !$is_followed;
        }

        $question = QuestionService::getQuestionByQuestionId($question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $question_id);
        }

        return $this->renderPartial(
            '_question_follow',
            [
                'id'           => $question_id,
                'count_follow' => $question['count_follow'],
                'is_followed'  => $is_followed,
            ]
        );
    }

    public function actionFavorite($question_id)
    {
        $is_favorite = FavoriteService::checkUseIsFavoriteQuestion($question_id, Yii::$app->user->id);

        if ($is_favorite) {
            FavoriteService::removeQuestionFavorite(
                $question_id,
                Yii::$app->user->id
            );
        } else {
            FavoriteService::addFavoriteQuestion($question_id, Yii::$app->user->id);
        }

        $question = QuestionService::getQuestionByQuestionId($question_id);

        if (!$question) {
            throw new NotFoundModelException('question', $question_id);
        }

        return $this->renderPartial(
            '_question_favorite',
            [
                'id'             => $question_id,
                'count_favorite' => $question['count_favorite'],
                'count_views'    => $question['count_views'],
                'is_favorite'    => !$is_favorite,
            ]
        );
    }

    public function actionAnonymous($id)
    {
        $question = QuestionService::getQuestionByQuestionId($id);
        if ($question['is_anonymous'] == QuestionEntity::STATUS_ANONYMOUS) {
            QuestionService::cancelAnonymous($id);
        } else {
            QuestionService::setAnonymous($id);
        }

        $this->redirect(['question/view', 'id' => $id]);
    }

    public function actionVote($id, $vote)
    {
        $vote_status = VoteService::getQuestionVoteStatus($id, Yii::$app->user->id);

        if ($vote_status !== false) {
            VoteService::updateQuestionVote(
                $id,
                Yii::$app->user->id,
                $vote
            );
        } else {
            VoteService::addQuestionVote($id, Yii::$app->user->id, $vote);
        }

        $question = QuestionService::getQuestionByQuestionId($id);

        if ($question === false) {
            throw new NotFoundModelException('question', $id);
        }


        return $this->renderPartial(
            '_question_vote',
            [
                'id'          => $id,
                'count_vote'  => $question['count_like'] - $question['count_hate'],
                'vote_status' => $vote,
            ]
        );
    }

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
    
    public function actionHottest()
    {
        $pages = new Pagination(
            [
                'totalCount' => QuestionService::fetchCount('hottest', ServerHelper::checkIsSpider()),
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
                'active'        => 'hottest',
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

    public function actionView($id, $sort = 'default', $answer_id = null, $comment_id = null)
    {
        $question_data = QuestionService::getQuestionByQuestionId($id);
        
        if (ServerHelper::checkIsSpider() &&
            !in_array($question_data['status'], explode(',', QuestionEntity::STATUS_DISPLAY_FOR_SPIDER))
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
        Counter::questionAddView($id);

        #回答
        if ($answer_id) {
            $pages = null;
            $answers_data = AnswerService::getAnswerListByAnswerId([$answer_id]);
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
            
            $answers_data = AnswerService::getAnswerListByQuestionId(
                $id,
                Yii::$app->request->get('answer-page', 1),
                Yii::$app->request->get('answer-per-page', 10),
                $sort
            );
        }

        foreach ($answers_data as &$answer) {
            if (!Yii::$app->user->isGuest) {
                $answer['vote_status'] = VoteService::getAnswerVoteStatus($answer['id'], Yii::$app->user->id);
            } else {
                $answer['vote_status'] = false;
            }
            $answer['count_vote'] = $answer['count_like'] - $answer['count_hate'];
        }

        /*print_r($answer_data);
        exit;*/


        if (Yii::$app->user->isGuest) {
            $is_followed = $is_favorite = $vote_status = false;
        } else {
            //是否已关注此问题
            $is_followed = FollowService::checkUseIsFollowedQuestion($id, Yii::$app->user->id);

            //是否已收藏此问题
            $is_favorite = FavoriteService::checkUseIsFavoriteQuestion($id, Yii::$app->user->id);

            //是否已对问题投票
            $vote_status = VoteService::getQuestionVoteStatus($id, Yii::$app->user->id);
        }

        #评论
        if ($answer_id && $comment_id) {
            $comments_data = [CommentService::getCommentByCommentId($comment_id)];

            foreach ($comments_data as &$comment) {
                if (!Yii::$app->user->isGuest) {
                    $comment['vote_status'] = VoteService::getAnswerCommentVoteStatus(
                        $comment['id'],
                        Yii::$app->user->id
                    );
                } else {
                    $comment['vote_status'] = false;
                }
                $comment['count_vote'] = $comment['count_like'] - $comment['count_hate'];
            }
        } else {
            $comments_data = null;
        }


        return $this->render(
            'view',
            [
                'question_data'    => $question_data,
                'answer_model'     => $answer_model,
                'answer_item_html' => $answers_data ? $this->renderPartial(
                    '_question_answer_item',
                    [
                        'question_id'       => $id,
                        'data'              => $answers_data,
                        'comment_item_html' => $comments_data ? $this->renderPartial(
                            '/answer/_answer_comment_item',
                            [
                                'question_id' => $id,
                                'answer_id'   => $answer_id,
                                'data'        => $comments_data,
                            ]
                        ) : '',
                    ]
                ) : '',
                'sort'             => $sort,
                'pages'            => $pages,
                'similar_question' => $similar_question,
                'is_followed'      => $is_followed,
                'is_favorite'      => $is_favorite,
                'vote_status'      => $vote_status,
            ]
        );
    }

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

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->created_by != Yii::$app->user->id) {
            throw new PermissionDeniedException();
        }

        $model->setScenario('common_edit');

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

    public function actionCommonEdit($id)
    {
        $model = $this->findModel($id);

        if ($model->created_by == Yii::$app->user->id) {
            throw new PermissionDeniedException();
        }

        $model->setScenario('common_edit');

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
     * @param $id
     * @return QuestionEntity the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = QuestionEntity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetAtWhoUserList($user_id, $question_id = null)
    {
        $user_ids = [];
        if ($question_id) {
            //关注此问题的好友
            /*$follow_user_ids = FollowService::getFollowQuestionUserIdsByQuestionId($question_id);
            if($follow_user_ids){
                $user_ids = array_merge($user_ids, $follow_user_ids);
            }*/

            //回答此问题的好友
            $answer_user_ids = AnswerService::getAnswerUserIdsByQuestionId($question_id);
            if ($answer_user_ids) {
                $user_ids = array_merge($user_ids, $answer_user_ids);
            }
        }

        /*if ($user_id) {
            //用户好友
            $user_friend_user_ids = FollowService::getUserFriendsUserId($user_id);
            if ($user_friend_user_ids) {
                $user_ids = array_merge($user_ids, $user_friend_user_ids);
            }
        }*/

        $user = UserService::getUserListByIds($user_ids);
        $at_user_data = [];

        foreach ($user as $item) {
            $at_user_data[] = [
                'id'       => $item['id'],
                'username' => $item['username'],
            ];
        }

        $this->jsonOut($at_user_data);
    }

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
                $result = false;
                throw new Exception(sprintf('暂未支持 %s 通知', $method));
        }
        
        $this->jsonOut(Error::get($result));
    }
    
    public function getSimilarQuestionBySubject($subject)
    {
        $similar_question = QuestionService::searchQuestionBySubject($subject);
        
        $this->jsonOut(Error::get($similar_question));
    }
    
    public function actionExplore()
    {
        
    }

    public function actionDelete($id)
    {
        $question = QuestionService::getQuestionByQuestionId($id);

        //本人，且没有回答
        if ($question['created_by'] == Yii::$app->user->id && $question['count_answer'] > 0) {
            Url::remember(Yii::$app->request->getReferrer());

            //删除问题
            QuestionEntity::findOne($id)->delete();

            return $this->goBack();
        } else {
            throw new PermissionDeniedException();
        }
    }

    public function actionVersionRepository($id)
    {
        $pages = new Pagination(
            [
                'totalCount'      => QuestionVersionEntity::find()->where(['question_id' => $id])->count(),
                'defaultPageSize' => 20,
                'params'          => array_merge($_GET, ['#' => '']),
            ]
        );

        $model = QuestionVersionEntity::find()->where(
            ['question_id' => $id]
        )->offset($pages->offset)->limit($pages->limit)->all();

        $question = QuestionService::getQuestionByQuestionId($id);

        return $this->render(
            'version_repository',
            [
                'model'    => $model,
                'question' => $question,
                'pages'    => $pages,
            ]
        );
    }
}
