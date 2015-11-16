<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/13
 * Time: 19:25
 */

namespace common\components;


use common\helpers\ServerHelper;
use common\helpers\TimeHelper;
use yii\base\Component;
use Yii;

class Monitor extends Component
{
    const STATUS_NORMAL = 0; #正常状态
    const STATUS_CURFEW = 1; #宵禁，规定时间，禁止注册，禁止提问，禁止回答
    const STATUS_MARTIAL_LAW = 2; #戒严，禁止注册，禁止登陆，禁止提问，禁止回答
    const STATUS_SHUTDOWN = 3; #关闭系统，禁止一切访问
    const STATUS_SPIDER = 99; #搜索引擎模式

    const OPEN_TIME_START = 8; #开放开始时间
    const OPEN_TIME_END = 24; #开放结束时间

    private static $status;

    #宵禁禁止的动作
    private static $curfew_actions = [
        'user_login'      => 'user/security/login',
        'user_register'   => 'user/registration/register',
        'question_create' => 'question/create',
        'question_update' => 'question/update',
        'answer_create'   => 'answer/create',
        'answer_update'   => 'answer/update',
    ];

    #戒严禁止的动作，todo　暂未启用
    private static $martial_law_actions = [
        'user_login'      => 'user/security/login',
        'question_create' => 'question/create',
        'question_update' => 'question/update',
    ];


    /**
     * 以下动作不需要进行监视
     * １、来自搜索引擎
     * 待补充 todo
     */
    public static function startMonitor()
    {
        if (self::$status === null) {
            if (ServerHelper::checkIsSpider()) {
                self::$status = self::STATUS_SPIDER;
            } else {
                $hour = date('G', TimeHelper::getCurrentTime());
                if ($hour < self::OPEN_TIME_START || $hour > self::OPEN_TIME_END) {
                    self::$status = self::STATUS_CURFEW;
                } else {
                    self::$status = self::STATUS_NORMAL;
                }
            }
        }

        #todo，关闭系统模式下，直接跳转
        if (self::$status == self::STATUS_SHUTDOWN) {

        }
    }

    public static function checkMonitorStatus()
    {
        if (!empty(Yii::$app->controller)) {
            $controller = Yii::$app->controller->id;
        }

        if (!empty(Yii::$app->controller)) {
            $action = Yii::$app->controller->action->id;
        }

        if (!empty(Yii::$app->controller->module)) {
            $module = Yii::$app->controller->module->id;
            if (strpos($module, 'app') === 0) {
                $module = null;
            }
        }

        $router = trim(implode('/', [$module, $controller, $action]), '/');

        if (self::$status === self::STATUS_CURFEW && in_array($router, self::$curfew_actions)) {

            Yii::$app->session->setFlash(
                'danger',
                sprintf(
                    '当前为宵禁时间，期间禁止 [注册、提问、回答]。开放时间为：%d:00~%d:00，当前时间：%d:00',
                    self::OPEN_TIME_START,
                    self::OPEN_TIME_END,
                    date('G:i:s')
                )
            );

            Yii::$app->controller->goBack();
            Yii::$app->end();
        }

        if (self::$status === self::STATUS_MARTIAL_LAW && in_array($router, self::$martial_law_actions)) {

        }
    }
}