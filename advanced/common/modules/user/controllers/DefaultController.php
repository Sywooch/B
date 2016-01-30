<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2016/1/10
 * Time: 18:14
 */

namespace common\modules\user\controllers;

use common\controllers\BaseController;
use common\entities\AnswerEntity;
use common\entities\QuestionEntity;
use common\helpers\ArrayHelper;
use common\services\FollowService;
use common\services\QuestionService;
use common\services\UserService;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;

class DefaultController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionWelcome()
    {
        return $this->render('welcome');
    }

    public function actionFollow($user_id)
    {
        $is_followed = FollowService::checkUseIsFollowedUser($user_id, Yii::$app->user->id);

        if ($is_followed) {
            $result = FollowService::removeFollowUser($user_id, Yii::$app->user->id);
        } else {
            $result = FollowService::addFollowUser($user_id, Yii::$app->user->id);
        }

        //var_dump($result);exit('~~~~');

        //操作成功，才能反转是否已关注
        if ($result) {
            $is_followed = !$is_followed;
        }

        $user = UserService::getUserById($user_id);

        if (!$user) {
            throw new NotFoundModelException('user', $user_id);
        }

        return $this->renderPartial(
            '/profile/_user_follow',
            [
                'id'          => $user_id,
                'is_followed' => $is_followed,
                'count_fans'  => $user->count_fans,
            ]
        );
    }

    public function actionOwnerQuestion()
    {
        $active = 'owner';

        $user = UserService::getUserById(Yii::$app->user->id);

        $pages = new Pagination(
            [
                'totalCount'      => $user->count_question,
                'defaultPageSize' => 10,
                'params'          => array_merge($_GET),
            ]
        );

        $question_ids = QuestionEntity::find()->select(['id'])->where(
            [
                'created_by' => Yii::$app->user->id,
            ]
        )->offset($pages->offset)->limit($pages->limit)->orderBy('updated_at DESC')->column();

        $question_list = QuestionService::getQuestionListByQuestionIds($question_ids);

        return $this->render(
            'question',
            [
                'active' => $active,
                'data'   => $question_list,
            ]
        );
    }

    public function actionAnsweredQuestion()
    {
        $active = 'answered';

        $user = UserService::getUserById(Yii::$app->user->id);

        $pages = new Pagination(
            [
                'totalCount'      => $user->count_question,
                'defaultPageSize' => 10,
                'params'          => array_merge($_GET),
            ]
        );

        $answer = AnswerEntity::find()->select(['id', 'question_id'])->where(
            [
                'created_by' => Yii::$app->user->id,
            ]
        )->groupBy('question_id')->offset($pages->offset)->limit($pages->limit)->orderBy('updated_at DESC')->all();

        $question_list = QuestionService::getQuestionListByQuestionIds(ArrayHelper::getColumn($answer, 'question_id'));

        //
        foreach ($answer as $item) {
            $question_list[$item['question_id']]->answer_id = $item['id'];
        }

        return $this->render(
            'question',
            [
                'active' => $active,
                'data'   => $question_list,
            ]
        );
    }

    public function actionFollowedQuestion()
    {
        $active = 'followed';

        $user = UserService::getUserById(Yii::$app->user->id);

        $pages = new Pagination(
            [
                'totalCount'      => $user->count_question,
                'defaultPageSize' => 10,
                'params'          => array_merge($_GET),
            ]
        );

        $question_list = FollowService::getFollowQuestionListByUserId(
            Yii::$app->user->id,
            $pages->getPage(),
            $pages->getPageSize()
        );

        return $this->render(
            'question',
            [
                'active' => $active,
                'data'   => $question_list,
            ]
        );
    }
    
    public function actionOwnerFavorite()
    {
        $active = 'owner';

        return $this->render(
            'favorite',
            [
                'active' => $active,
                'data'   => '',
            ]
        );
    }


}