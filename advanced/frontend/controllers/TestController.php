<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/9/10
 * Time: 15:43
 */

namespace frontend\controllers;

use common\components\Counter;
use common\components\Curl;
use common\components\Error;
use common\components\Notifier;
use common\components\Updater;
use common\components\user\User;
use common\entities\AnswerEntity;
use common\entities\NotificationEntity;
use common\entities\QuestionEntity;
use common\entities\UserEntity;
use common\entities\VoteEntity;
use common\helpers\AtHelper;
use common\helpers\TemplateHelper;
use common\models\xunsearch\QuestionSearch;
use common\services\AnswerService;
use common\services\FollowService;
use common\services\NotificationService;
use common\services\QuestionService;
use common\services\TagService;
use common\services\UserService;
use common\services\VoteService;
use console\modules\crawler\controllers\crawlers\CrawlerBase;
use Yii;
use common\controllers\BaseController;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;


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
            //$crawler->launch();
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

    public function actionAnswer()
    {
        $question_id = 5;
        $answer_data = AnswerEntity::find()->select(
            [
                'id',
                'count_useful' => '`count_like`-`count_hate`',
                'created_at',
            ]
        )->where(
            ['question_id' => $question_id]
        )->asArray()->createCommand()->getRawSql();

        print_r($answer_data);
        exit;
    }

    public function actionAbc()
    {
        $data = Yii::$app->redis->lRange([REDIS_KEY_NOTIFIER, 200], 0, 10);

        print_r($data);

        exit;
    }

    public function actionNotifier()
    {

        $result = Notifier::build()->sync(true)->from(1)->to([1, 2])->notice(
            NotificationService::TYPE_ANSWER_AT_ME,
            1
        );

        var_dump($result->result);
        exit;

        /* $result = Notifier::build()->sync(false)->from(1)->to([1, 2])->notice(
             NotificationService::TYPE_ANSWER_AT_ME,
             1
         );

         var_dump($result);

         $data = Yii::$app->redis->lRange([REDIS_KEY_NOTIFIER, NotificationService::TYPE_ANSWER_AT_ME], 0, 10);

         print_r($data);

         exit;*/

        //$notifier = Notifier::build()->sync(false)->to(1)->email('a', 'b');
    }

    public function actionCounter()
    {
        $result = Counter::build()->sync(false)->set('user_profile', 1, 'user_id')->value('count_answer', 1)->execute();
        $result = Counter::build()->sync(false)->set('user_profile', 1, 'user_id')->value('count_question', 1)->execute(
        );

        $data = Yii::$app->redis->lRange([REDIS_KEY_COUNTER, 'user_profile'], 0, 10);

        print_r($data);


        var_dump($result);
    }


    public function actionUpdater()
    {
        $result = Updater::build()->sync(false)->table(QuestionEntity::tableName())->where(['id' => 1])->set(
            ['subject' => "我是标题'"]
        )->execute();

        print_r($result);

        $data = Yii::$app->redis->lRange([REDIS_KEY_UPDATER, QuestionEntity::tableName()], 0, 10);

        print_r($data);

        exit;
    }

    public function actionRedis()
    {
        /*echo '<pre />';
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


        print_r(Yii::$app->redis->mget(['m', ['a', 'b', 'd', 'c']]));*/

        /*$cache_key = [REDIS_KEY_QUESTION, 'ABC'];
        $result = Yii::$app->redis->hMset($cache_key, ['a' => 1,'b' => 1000]);
        var_dump(empty($result));*/


        //$result = QuestionService::getQuestionListByQuestionIds([2, 3, 4, 5]);
        $associate_id = 198;
        $user_id = 61;
        $cache_key = [REDIS_KEY_QUESTION_VOTE_USER_LIST, $associate_id];
        $cache_data = Yii::$app->redis->zScore($cache_key, $user_id);

        var_dump($cache_data);
    }

    public function actionUser()
    {
        /* @var $user UserEntity */
        /*$user = Yii::createObject(UserEntity::className());
        $data = $user->getUserById([1]);

        print_r($data);*/

        $result = UserService::getUserListByIds([1]);
        print_r($result);

        $result = UserService::getUserIdByUsername(['admin', '瞎猫']);
        print_r($result);


        $result = UserService::getUserByUsername(['admin', '瞎猫']);
        print_r($result);


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
        $result = UserService::getUserFansList(1);
        print_r($result);
        exit;


        $result = FollowService::getTagIdsWhichUserIsGoodAt(1);

        //$data = TagService::getTagIdByName( ['中国人', '大家好']);
        /*$data = TagService::getTagNameById([1, 2]);

        print_r($data);*/

        //$result = TagService::getHotTag();
        //$result = TagService::getRelateTag(6);

        $result = TagService::getTagIdByName(['深圳游玩1', '情侣游玩1', '深户1', 'bbaacc']);

        print_r($result);
    }

    public function actionFindAt()
    {
        $string = '你好呀@小明,你又是谁呢？ @王宝强　 @小不点 ';
        $result = AtHelper::findAtUsername($string);

        print_r($result);
    }

    public function actionXunsearch()
    {
        echo '<pre />';
        /*$subjects = [
            '深圳户口如何办理？',
            '深圳目前有多少人口？',
            '深圳目前有多少外来人口？',
            '深圳目前有多少本地人口？',
            '深圳单身人口有多少？',
            '深圳有哪些商圈？',
            '深圳有哪些奇怪的现象？',
            '深圳有哪些奇怪的人？',
            '深圳有哪些奇怪的名人？',
        ];

        foreach ($subjects as $key => $subject) {
            $question = new Question();
            $question->id = ++$key;
            $question->subject = $subject . ' ' . date('Y-md-d H:i:s');
            $result = $question->save();
            var_dump($result);
        }*/

        $question = new QuestionSearch();
        $tags = $question->fenci('在深圳');

        print_r($tags);
        exit;
        //$result = $question->find()->where(['or','深圳','人口'])->all();

        $result = QuestionService::searchQuestionByTag($tags);

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


        $data = FollowService::getFollowQuestionUserIdsByQuestionId(10);

        print_r($data);
        $data = AnswerService::getAnswerUserIdsByQuestionId(5);

        print_r($data);


    }

    public function decodeSubject($subject)
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

        //$this->autoLoginById(1);
        /* @var $question QuestionEntity */
        $question = Yii::createObject(QuestionEntity::className());
        $question->subject = sprintf('现在时间为:%s', date('Y年m月d日 H:i:s'));
        $question->tags = 'aa,bb,cc';
        $question->content = 'test content';
        $result = $question->save();
        echo '<pre />';
        if ($question->hasErrors()) {
            print_r($question->getErrors());
        } else {
            //$question->trigger(QuestionEntity::EVENT_TEST);

            var_dump(Yii::$app->user->trigger(User::EVENT_USER_CREATE_QUESTION));
        }
        var_dump($result);
        print_r($question->getAttributes());
    }

    public function actionError()
    {
        Yii::error('abc', 'log');
    }

    public function actionCws()
    {
        $result = Yii::$app->cws->text(
            '红旗Linux是由北京中科红旗软件技术有限公司开发的一系列Linux发行版，包括桌面版、工作站版、数据中心服务器版、HA集群版和红旗嵌入式Linux等产品。目前在中国各软件专卖店可以购买到光盘版，同时官方网站也提供光盘镜像免费下载。红旗Linux是中国较大、较成熟的Linux发行版之一。'
        )->getTops();

        var_dump($result);

    }

    public function actionRbac()
    {
        //$result = $this->autoLoginById(3);
        //var_dump($result);

        echo '<pre />';
        echo Yii::$app->user->id;
        echo '<br />';

        var_dump(Yii::$app->user->can('elect'));


        //$count = Yii::$app->authManager->deleteAllCache();

        //var_dump($count);
    }

    public function actionNotice()
    {
        $arr = [
            'question_id' => 10,
            'answer_id'   => 10,
        ];

        //echo json_encode($arr);exit;

        Notifier::build()->sync(0)->from(1)->to(2)->notice(
            NotificationService::TYPE_FOLLOW_QUESTION_HAS_NEW_ANSWER,
            [
                'question_id' => 16,
            ]
        );
        Notifier::build()->sync(0)->from(2)->to(23)->notice(
            NotificationService::TYPE_FOLLOW_QUESTION_HAS_NEW_ANSWER,
            [
                'question_id' => 17,
            ]
        );

        $data = NotificationService::find()->orderBy('created_at DESC')->asArray()->all();

        $result = NotificationService::makeUpNotification($data);

        print_r($result);
    }

    public function actionCurl()
    {
        $curl = new Curl();

    }

    public function actionCurrency()
    {
        echo TemplateHelper::showHumanCurrency(1501);

    }

    public function actionAutoLogin()
    {
        $user_id = rand(1, UserService::MAX_OFFICIAL_ACCOUNT_ID);
        $result = $this->autoLoginById($user_id);

        if ($result) {
            $user = UserService::getUsernameByUserId($user_id);
            echo sprintf('当前登陆用户[id=>%s, username=>%s]', $user_id, $user);
        }
    }

    public function actionTransaction()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /* @var $question QuestionEntity */
            $question = QuestionEntity::findOne(2);
            echo '<h3>1</h3>';
            var_dump($question->subject);
            $question->subject = '深圳人喜欢吃什么？';
            $result = $question->save();

            print_r($question->getErrors());

            $question = QuestionEntity::findOne(2);
            echo '<h3>2</h3>';
            var_dump($question->subject);


            $transaction->commit();

            $question = QuestionEntity::findOne(2);
            echo '<h3>3</h3>';
            var_dump($question->subject);

        } catch (\Exception $e) {
            $transaction->rollBack();
            print_r($e->getMessage());
        }

        $question = QuestionEntity::findOne(2);
        echo '<h3>4</h3>';
        var_dump($question->subject);


    }

    public function actionVote()
    {
        /*$vote = VoteEntity::find()->select(['vote'])->where(
            [
                'type'         => 'question',
                'associate_id' => 198,
                'created_by'   => 2282,
            ]
        )->scalar();

        var_dump($vote);*/
        $type = 'question';
        $associate_id = 198;
        $user_id = 347;
        $vote = VoteService::getUseVoteStatus($type, $associate_id, $user_id);
        var_dump($vote);
    }
}

