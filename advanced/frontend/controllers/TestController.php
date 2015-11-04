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
use common\components\Error;
use common\components\Notifier;
use common\components\Updater;
use common\entities\AnswerEntity;
use common\entities\FollowTagPassiveEntity;
use common\entities\FollowUserEntity;
use common\entities\NotificationEntity;
use common\entities\PrivateMessageEntity;
use common\entities\QuestionEntity;
use common\entities\TagEntity;
use common\entities\UserEntity;
use common\helpers\AtHelper;
use common\helpers\FormatterHelper;
use common\models\xunsearch\Question;
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
        $data = $user->getUserById([1, 2]);

        print_r($data);
    }

    public function actionUpdateUser()
    {
        /* @var $user UserEntity */
        $user = Yii::createObject(UserEntity::className());
        $data = $user->updateUserCache(1, ['username' => 'new']);

        print_r($data);
    }


    public function actionTag()
    {
        /* @var $tag TagEntity */
        $tag = Yii::createObject(TagEntity::className());
        //$data = $tag->getTagIdByName( ['中国人', '大家好']);
        $data = $tag->getTagNameById([1, 2]);

        print_r($data);
    }

    public function actionFindAt()
    {
        $string = '你好呀@小明,你又是谁呢？ @王宝强　 @小不点 ';
        $result = AtHelper::findAtUsername($string);

        print_r($result);
    }


    public function actionTest()
    {

        /*$question = new Question();
        $question->id = 1;
        $question->subject = 'abc';
        $question->save();*/

        /*$answer = new AnswerEntity;
        $answer->question_id = 4;
        $answer->content = 'aa';

        $result = $answer->save();

        var_dump($result);
        print_r($answer->getErrors());
        exit('dd');*/

        //        $server = new \Fetch\Server('imap.exmail.qq.com');
        //        $server->setAuthentication('admin@bo-u.cn', '662800Yu');
        //
        //        echo '<pre />';
        //        $box = $server->getMailBox();
        //        var_dump($box);
        //
        //        $messages = $server->getRecentMessages(100);
        //        /** @var $message \Fetch\Message */
        //        foreach ($messages as $message) {
        //            print_r($message->getHeaders()->fromaddress);
        //            echo PHP_EOL;
        //            print_r($message->getHeaders()->toaddress);
        //            echo PHP_EOL;
        //            print_r($this->decodeSubject($message->getHeaders()->subject));
        //            echo PHP_EOL, PHP_EOL, PHP_EOL;
        //        }{imap.gmail.com:993/imap/ssl}INBOX


    }

    function decodeSubject($subject)
    {
        $data = imap_mime_header_decode($subject);

        $new_subject = [];
        foreach ($data as $item) {
            if ($item->charset != 'default' && $item->charset != 'UTF-8') {
                $item->text = mb_convert_encoding($item->text, 'utf8', $item->charset);
            }
            $new_subject[] = $item->text;
        }

        return implode('', $new_subject);
    }

    public function actionQuestion()
    {
        $this->setPerformanceRecordAnchor('start question');
        /* @var $question QuestionEntity */
        $question = Yii::createObject(QuestionEntity::className());
        $question->subject = 'testtesttesttesttesttesttest';
        $question->tags = 'aa,bb,cc';
        $question->content = 'testcontent';
        $result = $question->save();
        $this->setPerformanceRecordAnchor('end question');
        echo '<pre />';
        print_r($question->getErrors());
        var_dump($result);


    }
}

