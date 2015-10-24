<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 15:43
 */

namespace frontend\controllers;

use common\behaviors\PrivateMessageDialogBehavior;
use common\components\Counter;
use common\components\Notifier;
use common\components\Updater;
use common\entities\NotificationEntity;
use common\entities\PrivateMessageEntity;
use common\entities\QuestionEntity;
use common\entities\UserEntity;
use common\helpers\FormatterHelper;
use console\modules\crawler\controllers\crawlers\CrawlerBase;
use Yii;
use common\controllers\BaseController;
use yii\helpers\Inflector;


class TestController extends BaseController
{
    /*public function behaviors()
    {
        $behaviors = parent::behaviors();
        return ArrayHelper::merge($behaviors, [
                [
                        'class'    => 'yii\filters\HttpCache',
                        'only'     => ['index'],
                        //'cacheControlHeader' => 'public, max-age=3600',
                        'etagSeed' => function ($action, $params) {
                            $data = md5('abc');
                            return $data;
                        },
                ]
        ]);
    }*/

    public function actionIndex()
    {
        $crawler_sign = Inflector::camelize('sz_ben_di_bao');

        $crawler = Yii::createObject(
            [
                'class' => 'console\\modules\\crawler\\controllers\\crawlers\\' . $crawler_sign,
                'id'    => 1,
            ]
        );

        print_r($crawler);

        if ($crawler instanceof CrawlerBase) {
            //$crawler->up();
            $crawler->launch();
            //$crawler->down();
        }

        /*sleep(0.5);
        $this->setPerformanceRecordAnchor('action index');
        sleep(0.5);*/
        /*$data = Yii::$app->error->common;
        print_r($data);*/
        //$this->errorParam();
        //echo Yii::$app->setting->get('test');
        #$this->render('index');
    }

    public function actionMail()
    {
        $result = Yii::$app->mailer->compose()->setFrom(Yii::$app->params['senderEmail'])->setTo(
            '6202551@qq.com'
        )->setSubject('This is a test mail ')->send();

        var_dump($result);
    }

    public function actionAbc()
    {
        $data = Yii::$app->redis->lRange([REDIS_KEY_NOTIFIER, 200], 0, 10);

        print_r($data);

        exit;
    }

    public function actionNotifier()
    {

        $result = Notifier::build()->priority(true)->from(1)->to([1, 2])->set(
            NotificationEntity::TYPE_ANSWER_AT_ME,
            1
        )->send();

        $result = Notifier::build()->priority(false)->from(1)->to([1, 2])->set(
            NotificationEntity::TYPE_ANSWER_AT_ME,
            1
        )->send();

        var_dump($result);

        $data = Yii::$app->redis->lRange([REDIS_KEY_NOTIFIER, NotificationEntity::TYPE_ANSWER_AT_ME], 0, 10);

        print_r($data);

        exit;
    }

    public function actionCounter()
    {
        $result = Counter::build()->priority(false)->set('user_profile', 1, 'user_id')->value(
            'count_answer',
            1
        )->execute();
        $result = Counter::build()->priority(false)->set('user_profile', 1, 'user_id')->value(
            'count_question',
            1
        )->execute();

        $data = Yii::$app->redis->lRange([REDIS_KEY_COUNTER, 'user_profile'], 0, 10);

        print_r($data);


        var_dump($result);
    }


    public function actionUpdater()
    {
        $result = Updater::build()->priority(false)->table(QuestionEntity::tableName())->where(['id' => 1])->set(
            ['subject' => "我是标题'"]
        )->execute();

        print_r($result);

        $data = Yii::$app->redis->lRange([REDIS_KEY_UPDATER, QuestionEntity::tableName()], 0, 10);

        print_r($data);

        exit;
    }

    public function actionRedis()
    {
        echo '<pre />';
        Yii::$app->redis->set(['s', 'test'], 'value:a');
        print_r(Yii::$app->redis->get(['s', 'test']));
        echo '<hr />';


        Yii::$app->redis->set(['s', 1], 'value:a1');
        print_r(Yii::$app->redis->get(['s', 1]));

        echo '<hr />';


        Yii::$app->redis->mset(
            [
                'm',
                [
                    'a' => 'mset:11',
                    'b' => 'mset:22',
                    'c' => 'mset:3',

                ],
            ]
        );


        print_r(Yii::$app->redis->mget(['m', ['a', 'b', 'd', 'c']]));

    }

    public function actionGetUser()
    {
        /* @var $user UserEntity */
        $user = Yii::createObject(UserEntity::className());
        $data = $user->getUserById([1]);

        print_r($data);
    }

    public function actionGetUsername()
    {
        /* @var $user UserEntity */
        $user = Yii::createObject(UserEntity::className());
        $data = $user->getUserById([1,2]);

        print_r($data);
    }

    public function actionUpdateUser()
    {
        /* @var $user UserEntity */
        $user = Yii::createObject(UserEntity::className());
        $data = $user->updateUserCache(1, ['username' => 'new']);

        print_r($data);
    }
}

