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
        $user_event_log_list = UserService::getUserEventLogList($user['id'], Yii::$app->user->id);

        if ($user_event_log_list) {
            $user_event_list = UserService::getUserEventByEventIds(
                ArrayHelper::getColumn($user_event_log_list, 'user_event_id')
            );
        } else {
            $user_event_list = [];
        }


        $tag_list = UserService::getUserBeGoodAtTags($user->id);
        $fans_list = UserService::getUserFansList($user->id, 1, 8);


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
            ]
        );
    }
}