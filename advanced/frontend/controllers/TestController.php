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
use common\config\RedisKey;
use common\entities\AnswerEntity;
use common\entities\NotificationEntity;
use common\entities\QuestionEntity;
use common\entities\UserEntity;
use common\entities\UserGradeRuleEntity;
use common\entities\VoteEntity;
use common\helpers\AtHelper;
use common\helpers\TemplateHelper;
use common\helpers\TimeHelper;
use common\models\CacheUserEventModel;
use common\models\CacheUserGradeModel;
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

    public function actionAt()
    {
        $content = '@瞎猫 46';
        $username = AtHelper::findAtUsername($content);

        print_r($username);
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

         $data = Yii::$app->redis->lRange([RedisKey::REDIS_KEY_NOTIFIER, NotificationService::TYPE_ANSWER_AT_ME], 0, 10);

         print_r($data);

         exit;*/

        //$notifier = Notifier::build()->sync(false)->to(1)->email('a', 'b');
    }

    public function actionCounter()
    {
        $result = Counter::build()->sync(false)->set('user_profile', 1, 'user_id')->value('count_answer', 1)->execute();
        $result = Counter::build()->sync(false)->set('user_profile', 1, 'user_id')->value('count_question', 1)->execute(
        );

        $data = Yii::$app->redis->lRange([RedisKey::REDIS_KEY_COUNTER, 'user_profile'], 0, 10);

        print_r($data);


        var_dump($result);
    }


    public function actionUpdater()
    {
        $result = Updater::build()->sync(false)->table(QuestionEntity::tableName())->where(['id' => 1])->set(
            ['subject' => "我是标题'"]
        )->execute();

        print_r($result);

        $data = Yii::$app->redis->lRange([RedisKey::REDIS_KEY_UPDATER, QuestionEntity::tableName()], 0, 10);

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
        /*$associate_id = 198;
        $user_id = 61;
        $cache_key = [RedisKey::REDIS_KEY_QUESTION_VOTE_USER_LIST, $associate_id];
        $cache_data = Yii::$app->redis->zScore($cache_key, $user_id);

        var_dump($cache_data);*/

        //检查保存到redis为一个对象，取的时候是否还是一个对象类型
        $rules = UserGradeRuleEntity::find()->where(
            ['status' => UserGradeRuleEntity::STATUS_ENABLE]
        )->orderBy('credit ASC')->all();
        $data = [];


        foreach ($rules as $rule) {
            $data[$rule->id] = (new CacheUserGradeModel())->filter($rule);
        }


        $a = new \stdClass();
        $a->name = '小明';
        $a->age = 30;

        $cache_key = [RedisKey::REDIS_KEY_USER_GRADE_RULE];
        $cache_data = Yii::$app->redis->set($cache_key, $data);

        $data = Yii::$app->redis->get($cache_key);

        print_r($data['age']);

    }

    public function actionUser()
    {
        /* @var $user UserEntity */
        /*$user = Yii::createObject(UserEntity::className());
        $data = $user->getUserById([1]);

        print_r($data);*/

        $result = UserService::getUserListByIds([1]);
        print_r($result);

        /* $result = UserService::getUserIdByUsername(['admin', '瞎猫']);
         print_r($result);


         $result = UserService::getUserByUsername(['admin', '瞎猫']);
         print_r($result);*/


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
        $result = UserService::autoLoginById($user_id);

        if ($result) {
            $user = UserService::getUsernameByUserId($user_id);
            echo sprintf('当前登陆用户["id" => "%s", "username" => "%s"]', $user_id, $user);
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
        $vote = VoteService::getUserVoteStatus($type, $associate_id, $user_id);
        var_dump($vote);
    }

    public function actionTypeahead()
    {
        return $this->render(
            'typeahead'
        );
    }

    public function actionData($search = null)
    {
        $data = [
            ['value' => 'a', 'name' => 'a', 'description' => 'description'],
            ['value' => 'admin', 'name' => 'a', 'description' => 'description'],
            ['value' => 'b', 'name' => 'a', 'description' => 'description'],
            ['value' => 'big', 'name' => 'a', 'description' => 'description'],
            ['value' => 'bean', 'name' => 'a', 'description' => 'description'],
        ];

        $this->jsonOut($data);
    }

    public function actionTemp()
    {
        $credit = 1;
        $currency = 2;
        $formula = '$credit * 1 + $currency * 2';
        $credit_calculator = create_function('$credit, $currency', 'return ' . $formula . ';');


        //算总分　根据 credit + currency 计算
        echo $credit_calculator($credit, $currency);
    }

    public function actionTime()
    {
        echo '<p>今天时间范围</p>';
        echo sprintf(
            '<p>%s ~ %s</p>',
            date('Y-m-d H:i:s', TimeHelper::getTodayStartTime()),
            date(
                'Y-m-d H:i:s',
                TimeHelper::getTodayEndTime()
            )
        );
        echo '<p>本周时间范围</p>';
        echo sprintf(
            '<p>%s ~ %s</p>',
            date('Y-m-d H:i:s', TimeHelper::getThisWeekStartTime()),
            date('Y-m-d H:i:s', TimeHelper::getThisWeekEndTime())
        );
        echo '<p>本月时间范围</p>';
        echo sprintf(
            '<p>%s ~ %s</p>',
            date('Y-m-d H:i:s', TimeHelper::getThisMonthStartTime()),
            date('Y-m-d H:i:s', TimeHelper::getThisMonthEndTime())
        );
        echo '<p>本季度时间范围</p>';
        echo sprintf(
            '<p>%s ~ %s</p>',
            date('Y-m-d H:i:s', TimeHelper::getThisSeasonStartTime()),
            date('Y-m-d H:i:s', TimeHelper::getThisSeasonEndTime())
        );
        echo '<p>本年度时间范围</p>';
        echo sprintf(
            '<p>%s ~ %s</p>',
            date('Y-m-d H:i:s', TimeHelper::getThisYearStartTime()),
            date(
                'Y-m-d H:i:s',
                TimeHelper::getThisYearEndTime()
            )
        );

    }

    public function actionCacheModel()
    {
        $data = [
            'id'    => 1,
            'name'  => 1,
            'event' => 1,
            'template' => 1,
            'abc'   => 1,
        ];

        $result = (new CacheUserEventModel)->filter($data);

        print_r($result);

        $result = (new CacheUserEventModel)->build($result);
        print_r($result);


    }

    public function actionDomain()
    {
        $data = [
            'a'      => -20319,
            'ai'     => -20317,
            'an'     => -20304,
            'ang'    => -20295,
            'ao'     => -20292,
            'ba'     => -20283,
            'bai'    => -20265,
            'ban'    => -20257,
            'bang'   => -20242,
            'bao'    => -20230,
            'bei'    => -20051,
            'ben'    => -20036,
            'beng'   => -20032,
            'bi'     => -20026,
            'bian'   => -20002,
            'biao'   => -19990,
            'bie'    => -19986,
            'bin'    => -19982,
            'bing'   => -19976,
            'bo'     => -19805,
            'bu'     => -19784,
            'ca'     => -19775,
            'cai'    => -19774,
            'can'    => -19763,
            'cang'   => -19756,
            'cao'    => -19751,
            'ce'     => -19746,
            'ceng'   => -19741,
            'cha'    => -19739,
            'chai'   => -19728,
            'chan'   => -19725,
            'chang'  => -19715,
            'chao'   => -19540,
            'che'    => -19531,
            'chen'   => -19525,
            'cheng'  => -19515,
            'chi'    => -19500,
            'chong'  => -19484,
            'chou'   => -19479,
            'chu'    => -19467,
            'chuai'  => -19289,
            'chuan'  => -19288,
            'chuang' => -19281,
            'chui'   => -19275,
            'chun'   => -19270,
            'chuo'   => -19263,
            'ci'     => -19261,
            'cong'   => -19249,
            'cou'    => -19243,
            'cu'     => -19242,
            'cuan'   => -19238,
            'cui'    => -19235,
            'cun'    => -19227,
            'cuo'    => -19224,
            'da'     => -19218,
            'dai'    => -19212,
            'dan'    => -19038,
            'dang'   => -19023,
            'dao'    => -19018,
            'de'     => -19006,
            'deng'   => -19003,
            'di'     => -18996,
            'dian'   => -18977,
            'diao'   => -18961,
            'die'    => -18952,
            'ding'   => -18783,
            'diu'    => -18774,
            'dong'   => -18773,
            'dou'    => -18763,
            'du'     => -18756,
            'duan'   => -18741,
            'dui'    => -18735,
            'dun'    => -18731,
            'duo'    => -18722,
            'e'      => -18710,
            'en'     => -18697,
            'er'     => -18696,
            'fa'     => -18526,
            'fan'    => -18518,
            'fang'   => -18501,
            'fei'    => -18490,
            'fen'    => -18478,
            'feng'   => -18463,
            'fo'     => -18448,
            'fou'    => -18447,
            'fu'     => -18446,
            'ga'     => -18239,
            'gai'    => -18237,
            'gan'    => -18231,
            'gang'   => -18220,
            'gao'    => -18211,
            'ge'     => -18201,
            'gei'    => -18184,
            'gen'    => -18183,
            'geng'   => -18181,
            'gong'   => -18012,
            'gou'    => -17997,
            'gu'     => -17988,
            'gua'    => -17970,
            'guai'   => -17964,
            'guan'   => -17961,
            'guang'  => -17950,
            'gui'    => -17947,
            'gun'    => -17931,
            'guo'    => -17928,
            'ha'     => -17922,
            'hai'    => -17759,
            'han'    => -17752,
            'hang'   => -17733,
            'hao'    => -17730,
            'he'     => -17721,
            'hei'    => -17703,
            'hen'    => -17701,
            'heng'   => -17697,
            'hong'   => -17692,
            'hou'    => -17683,
            'hu'     => -17676,
            'hua'    => -17496,
            'huai'   => -17487,
            'huan'   => -17482,
            'huang'  => -17468,
            'hui'    => -17454,
            'hun'    => -17433,
            'huo'    => -17427,
            'ji'     => -17417,
            'jia'    => -17202,
            'jian'   => -17185,
            'jiang'  => -16983,
            'jiao'   => -16970,
            'jie'    => -16942,
            'jin'    => -16915,
            'jing'   => -16733,
            'jiong'  => -16708,
            'jiu'    => -16706,
            'ju'     => -16689,
            'juan'   => -16664,
            'jue'    => -16657,
            'jun'    => -16647,
            'ka'     => -16474,
            'kai'    => -16470,
            'kan'    => -16465,
            'kang'   => -16459,
            'kao'    => -16452,
            'ke'     => -16448,
            'ken'    => -16433,
            'keng'   => -16429,
            'kong'   => -16427,
            'kou'    => -16423,
            'ku'     => -16419,
            'kua'    => -16412,
            'kuai'   => -16407,
            'kuan'   => -16403,
            'kuang'  => -16401,
            'kui'    => -16393,
            'kun'    => -16220,
            'kuo'    => -16216,
            'la'     => -16212,
            'lai'    => -16205,
            'lan'    => -16202,
            'lang'   => -16187,
            'lao'    => -16180,
            'le'     => -16171,
            'lei'    => -16169,
            'leng'   => -16158,
            'li'     => -16155,
            'lia'    => -15959,
            'lian'   => -15958,
            'liang'  => -15944,
            'liao'   => -15933,
            'lie'    => -15920,
            'lin'    => -15915,
            'ling'   => -15903,
            'liu'    => -15889,
            'long'   => -15878,
            'lou'    => -15707,
            'lu'     => -15701,
            'lv'     => -15681,
            'luan'   => -15667,
            'lue'    => -15661,
            'lun'    => -15659,
            'luo'    => -15652,
            'ma'     => -15640,
            'mai'    => -15631,
            'man'    => -15625,
            'mang'   => -15454,
            'mao'    => -15448,
            'me'     => -15436,
            'mei'    => -15435,
            'men'    => -15419,
            'meng'   => -15416,
            'mi'     => -15408,
            'mian'   => -15394,
            'miao'   => -15385,
            'mie'    => -15377,
            'min'    => -15375,
            'ming'   => -15369,
            'miu'    => -15363,
            'mo'     => -15362,
            'mou'    => -15183,
            'mu'     => -15180,
            'na'     => -15165,
            'nai'    => -15158,
            'nan'    => -15153,
            'nang'   => -15150,
            'nao'    => -15149,
            'ne'     => -15144,
            'nei'    => -15143,
            'nen'    => -15141,
            'neng'   => -15140,
            'ni'     => -15139,
            'nian'   => -15128,
            'niang'  => -15121,
            'niao'   => -15119,
            'nie'    => -15117,
            'nin'    => -15110,
            'ning'   => -15109,
            'niu'    => -14941,
            'nong'   => -14937,
            'nu'     => -14933,
            'nv'     => -14930,
            'nuan'   => -14929,
            'nue'    => -14928,
            'nuo'    => -14926,
            'o'      => -14922,
            'ou'     => -14921,
            'pa'     => -14914,
            'pai'    => -14908,
            'pan'    => -14902,
            'pang'   => -14894,
            'pao'    => -14889,
            'pei'    => -14882,
            'pen'    => -14873,
            'peng'   => -14871,
            'pi'     => -14857,
            'pian'   => -14678,
            'piao'   => -14674,
            'pie'    => -14670,
            'pin'    => -14668,
            'ping'   => -14663,
            'po'     => -14654,
            'pu'     => -14645,
            'qi'     => -14630,
            'qia'    => -14594,
            'qian'   => -14429,
            'qiang'  => -14407,
            'qiao'   => -14399,
            'qie'    => -14384,
            'qin'    => -14379,
            'qing'   => -14368,
            'qiong'  => -14355,
            'qiu'    => -14353,
            'qu'     => -14345,
            'quan'   => -14170,
            'que'    => -14159,
            'qun'    => -14151,
            'ran'    => -14149,
            'rang'   => -14145,
            'rao'    => -14140,
            're'     => -14137,
            'ren'    => -14135,
            'reng'   => -14125,
            'ri'     => -14123,
            'rong'   => -14122,
            'rou'    => -14112,
            'ru'     => -14109,
            'ruan'   => -14099,
            'rui'    => -14097,
            'run'    => -14094,
            'ruo'    => -14092,
            'sa'     => -14090,
            'sai'    => -14087,
            'san'    => -14083,
            'sang'   => -13917,
            'sao'    => -13914,
            'se'     => -13910,
            'sen'    => -13907,
            'seng'   => -13906,
            'sha'    => -13905,
            'shai'   => -13896,
            'shan'   => -13894,
            'shang'  => -13878,
            'shao'   => -13870,
            'she'    => -13859,
            'shen'   => -13847,
            'sheng'  => -13831,
            'shi'    => -13658,
            'shou'   => -13611,
            'shu'    => -13601,
            'shua'   => -13406,
            'shuai'  => -13404,
            'shuan'  => -13400,
            'shuang' => -13398,
            'shui'   => -13395,
            'shun'   => -13391,
            'shuo'   => -13387,
            'si'     => -13383,
            'song'   => -13367,
            'sou'    => -13359,
            'su'     => -13356,
            'suan'   => -13343,
            'sui'    => -13340,
            'sun'    => -13329,
            'suo'    => -13326,
            'ta'     => -13318,
            'tai'    => -13147,
            'tan'    => -13138,
            'tang'   => -13120,
            'tao'    => -13107,
            'te'     => -13096,
            'teng'   => -13095,
            'ti'     => -13091,
            'tian'   => -13076,
            'tiao'   => -13068,
            'tie'    => -13063,
            'ting'   => -13060,
            'tong'   => -12888,
            'tou'    => -12875,
            'tu'     => -12871,
            'tuan'   => -12860,
            'tui'    => -12858,
            'tun'    => -12852,
            'tuo'    => -12849,
            'wa'     => -12838,
            'wai'    => -12831,
            'wan'    => -12829,
            'wang'   => -12812,
            'wei'    => -12802,
            'wen'    => -12607,
            'weng'   => -12597,
            'wo'     => -12594,
            'wu'     => -12585,
            'xi'     => -12556,
            'xia'    => -12359,
            'xian'   => -12346,
            'xiang'  => -12320,
            'xiao'   => -12300,
            'xie'    => -12120,
            'xin'    => -12099,
            'xing'   => -12089,
            'xiong'  => -12074,
            'xiu'    => -12067,
            'xu'     => -12058,
            'xuan'   => -12039,
            'xue'    => -11867,
            'xun'    => -11861,
            'ya'     => -11847,
            'yan'    => -11831,
            'yang'   => -11798,
            'yao'    => -11781,
            'ye'     => -11604,
            'yi'     => -11589,
            'yin'    => -11536,
            'ying'   => -11358,
            'yo'     => -11340,
            'yong'   => -11339,
            'you'    => -11324,
            'yu'     => -11303,
            'yuan'   => -11097,
            'yue'    => -11077,
            'yun'    => -11067,
            'za'     => -11055,
            'zai'    => -11052,
            'zan'    => -11045,
            'zang'   => -11041,
            'zao'    => -11038,
            'ze'     => -11024,
            'zei'    => -11020,
            'zen'    => -11019,
            'zeng'   => -11018,
            'zha'    => -11014,
            'zhai'   => -10838,
            'zhan'   => -10832,
            'zhang'  => -10815,
            'zhao'   => -10800,
            'zhe'    => -10790,
            'zhen'   => -10780,
            'zheng'  => -10764,
            'zhi'    => -10587,
            'zhong'  => -10544,
            'zhou'   => -10533,
            'zhu'    => -10519,
            'zhua'   => -10331,
            'zhuai'  => -10329,
            'zhuan'  => -10328,
            'zhuang' => -10322,
            'zhui'   => -10315,
            'zhun'   => -10309,
            'zhuo'   => -10307,
            'zi'     => -10296,
            'zong'   => -10281,
            'zou'    => -10274,
            'zu'     => -10270,
            'zuan'   => -10262,
            'zui'    => -10260,
            'zun'    => -10256,
            'zuo'    => -10254,
        ];

        //print_r(array_keys($data));
        $domain_group = [];
        $domains = array_keys($data);

        $i = 0;
        $index = 0;
        do {
            $first = $domains[$index];

            foreach ($domains as $domain) {
                //$domain_group[] = $first . $domain;
                //echo $first . $domain, '<br />';

                if ($first != $domain && strlen($first) == 2 && strlen($domain) == 2) {
                    $domain_group[] = $first . $first . $first;
                    $domain_group[] = $first . $first . $domain;
                    $domain_group[] = $first . $domain . $domain;
                    $domain_group[] = $first . $domain . $first;
                    //$i++;
                }
            }
            $index++;
        } while ($index < count($domains));

        $result = file_put_contents('domain.txt', implode("\r\n", array_unique($domain_group)));

        print_r($result);

    }

    public function actionAndOr()
    {
        $a = 1;
        $b = 2;
        $c = 4;
        $d = 8;

        var_dump($a | $b);
        var_dump($a | $b | $c);
        var_dump($a | $b | $c | $d);

        var_dump(1 & $a);
        var_dump(3 & $b);
        var_dump(7 & $c);
        var_dump(15 & $d);
    }
}
