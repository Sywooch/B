<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:22
 */

namespace common\modules\user\controllers;

use common\helpers\TimeHelper;
use common\services\QuestionService;
use common\services\UserService;
use dektrium\user\controllers\ProfileController as BaseProfileController;
use Yii;
use yii\filters\AccessControl;

class ProfileController extends BaseProfileController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['index', 'show'], 'roles' => ['@']],
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
        $user_event_log = UserService::getUserEventLogList($user['id']);

        $tag_list = UserService::getUserBeGoodAtTags($user->id);


        //print_r($score_list);exit;
        return $this->render(
            'show',
            [
                'user'           => $user,
                'question_list'  => $question_list,
                'total_currency' => $total_currency,
                'total_credit'   => $total_credit,
                'score_list'     => $score_list,
                'user_event_log' => $user_event_log,
                'tag_list' => $tag_list,
            ]
        );
    }
}