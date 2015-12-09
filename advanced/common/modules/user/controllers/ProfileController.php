<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:22
 */

namespace common\modules\user\controllers;

use common\helpers\ArrayHelper;
use common\services\QuestionService;
use dektrium\user\controllers\ProfileController as BaseProfileController;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

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

    public function actionShow($id)
    {

        //todo 非好友，不允许查看

        $user = ArrayHelper::merge(Yii::$app->user->identity,[]);

        if (empty($user)) {
            throw new NotFoundHttpException();
        }

        $question_list = QuestionService::getQuestionListByUserId($id);

        return $this->render(
            'show',
            [
                'user'          => $user,
                'question_list' => $question_list,
            ]
        );
    }
}