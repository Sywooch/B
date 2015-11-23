<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/23
 * Time: 9:54
 */

namespace console\controllers;


use common\components\rbac\AuthorRule;
use Yii;
use yii\console\Controller;

class InitRbacController extends Controller
{

    public function actionIndex()
    {
        $string = 'O:33:"common\components\rbac\AuthorRule":3:{s:4:"name";s:8:"isAuthor";s:9:"createdAt";i:1448244664;s:9:"updatedAt";i:1448244664;}';

        $result =  unserialize($string);
        print_r($result);
    }

    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $rule = new AuthorRule;
        $auth->add($rule);

        // add the "updateOwnPost" permission and associate the rule with it.
        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update own post';
        $updateOwnPost->ruleName = $rule->name;
        $auth->add($updateOwnPost);

        // "updateOwnPost" will be used from "updatePost"
        //$auth->addChild($updateOwnPost, $updatePost);

        // allow "author" to update their own posts
        //$auth->addChild($author, $updateOwnPost)
    }
}