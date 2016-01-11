<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2016/1/10
 * Time: 18:14
 */

namespace common\modules\user\controllers;

use common\controllers\BaseController;

class DefaultController extends BaseController
{
    public function actionWelcome()
    {
        return $this->render('welcome');
    }
}