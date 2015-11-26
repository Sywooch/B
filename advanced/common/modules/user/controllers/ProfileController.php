<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/11
 * Time: 18:22
 */

namespace common\modules\user\controllers;

use common\services\QuestionService;
use dektrium\user\controllers\ProfileController as BaseProfileController;

class ProfileController extends BaseProfileController
{
    public function actionShow($id)
    {
        $profile = $this->finder->findProfileById($id);

        if ($profile === null) {
            throw new NotFoundHttpException();
        }

        $question_list = QuestionService::getQuestionListByUserId($id);

        return $this->render(
            'show',
            [
                'profile'       => $profile,
                'question_list' => $question_list,
            ]
        );
    }
}