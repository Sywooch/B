<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:22
 */

namespace common\modules\user\controllers;

use common\helpers\ArrayHelper;
use common\helpers\TimeHelper;
use common\models\CacheAnswerModel;
use common\services\AnswerService;
use common\services\FollowService;
use common\services\QuestionService;
use common\services\TagService;
use common\services\UserService;
use dektrium\user\controllers\ProfileController as BaseProfileController;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;

class ProfileController extends BaseProfileController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function actionShow($username)
    {
        //todo 非好友，不允许查看
        $user = UserService::getUserByUsername($username);
        $question_list = QuestionService::getQuestionListByUserId($user['id']);

        //积分记录
        $from = TimeHelper::getBeforeTime(79, true);
        $to = TimeHelper::getTodayEndTime();
        $score_list = UserService::getUserScoreList($user['id'], $from, $to);
        $total_currency = $total_credit = 0;
        foreach ($score_list as $score) {
            if ($score) {
                $total_currency += $score['currency'];
                $total_credit += $score['credit'];
            }
        }

        //动态列表
        $user_event_log_list = UserService::getUserEventLogList($user['id'], Yii::$app->user->id);

        if ($user_event_log_list) {
            $user_event_list = UserService::getUserEventByEventIds(
                ArrayHelper::getColumn($user_event_log_list, 'user_event_id')
            );
        } else {
            $user_event_list = [];
        }

        //print_r($user_event_list);exit;

        $tag_list = UserService::getUserBeGoodAtTags($user->id);
        $fans_list = UserService::getUserFansList($user->id, 1, 8);

        $follow_status = FollowService::checkUseIsFollowedUser(
            $user->id,
            Yii::$app->user->id
        );

        return $this->render(
            'show',
            [
                'user'                => $user,
                'question_list'       => $question_list,
                'total_currency'      => $total_currency,
                'total_credit'        => $total_credit,
                'score_list'          => $score_list,
                'user_event_log_list' => $user_event_log_list,
                'user_event_list'     => $user_event_list,
                'tag_list'            => $tag_list,
                'fans_list'           => $fans_list,
                'follow_status'       => $follow_status,
            ]
        );
    }

    public function actionOwnerQuestion($user_id)
    {
        $user = UserService::getUserById($user_id);
        $data = QuestionService::getQuestionListByUserId($user_id, 1, 30);

        if ($data) {
            $html = $this->renderPartial(
                '_question',
                [
                    'user' => $user,
                    'data' => $data,
                ]
            );
        } else {
            $html = '';
        }

        return Json::encode($html);
    }

    public function actionFollowedQuestion($user_id)
    {
        $data = FollowService::getFollowQuestionListByUserId($user_id, 1, 20);

        if ($data) {
            $html = $this->renderPartial(
                '_answer',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = '';
        }

        return Json::encode($html);
    }

    public function actionAnsweredQuestion($user_id)
    {
        $user = UserService::getUserById($user_id);
        $data = AnswerService::getAnswerListByUserId($user_id, 1, 20);

        if ($data) {
            $question_list = QuestionService::getQuestionListByQuestionIds(
                ArrayHelper::getColumn($data, 'question_id')
            );
            foreach ($data as $item) {
                /* @var $item CacheAnswerModel */
                $item->question = $question_list[$item->question_id];
            }
        }

        if ($data) {
            $html = $this->renderPartial(
                '_answer',
                [
                    'user' => $user,
                    'data' => $data,
                ]
            );
        } else {
            $html = '';
        }

        return Json::encode($html);
    }

    public function actionFollowedTag($user_id)
    {
        $data = FollowService::getUserFollowTagList($user_id, 1, 20);

        if ($data) {
            $html = $this->renderPartial(
                '_tag',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = '';
        }

        return Json::encode($html);
    }

    public function actionFollowedUser($user_id)
    {
        $data = FollowService::getUserFriendsUserList($user_id, 1, 20);

        if ($data) {
            $html = $this->renderPartial(
                '_user',
                [
                    'data' => $data,
                ]
            );
        } else {
            $html = '';
        }

        return Json::encode($html);
    }
}
