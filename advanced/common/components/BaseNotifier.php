<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/20
 * Time: 10:46
 */

namespace common\components;

use common\config\RedisKey;
use common\entities\NotificationEntity;
use common\exceptions\ParamsInvalidException;
use common\helpers\StringHelper;
use common\helpers\TimeHelper;
use common\models\AssociateModel;
use common\services\NotificationService;
use common\services\UserService;
use Yii;
use yii\base\Exception;
use yii\base\Object;
use yii\helpers\Json;

class BaseNotifier extends Object
{

    const METHOD_NOTICE = 'notice';
    const METHOD_EMAIL = 'email';
    const METHOD_SMS = 'sms';
    const METHOD_WEIXIN = 'weixin';

    public $result; #结果集
    private static $instance;
    private $immediate; #优先级, true 立马执行　false 队列
    private $sender; #谁发送
    private $receiver; #发送给谁
    private $method; #通知方法:notice,email,sms,weixin

    private $user_event_id;
    private $associate_type;
    private $associate_id;
    private $associate_data;

    public static function build()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        self::$instance->init();

        return self::$instance;
    }

    public function init()
    {
        $this->immediate = false;
        $this->sender = null;
        $this->receiver = null;
        $this->result = null;
        $this->method = null;
    }

    public function sync($immediate = true)
    {
        $this->immediate = $immediate;

        return $this;
    }

    public function from($sender = null)
    {
        $this->sender = $sender;

        return $this;
    }


    public function to($receiver)
    {
        $user_ids = [];

        if (is_array($receiver)) {
            $user_ids = $receiver;
        } elseif (is_numeric($receiver)) {
            $user_ids[] = $receiver;
        } elseif (count(explode(',', $receiver))) {
            $user_ids = explode(',', $receiver);
        }

        $this->receiver = $user_ids;

        return $this;
    }

    /**
     * 指通知对应的主体
     * 如：回答、评论、在问题或回答或评论中@用户，对应的主体是问题
     * 如：专栏文章评论对应的是专栏文章
     * @param       $associate_type 注意这个，如果associate_type是 answer\answer_comment ,需将主体转换为question
     * @param       $associate_id   主体ID
     * @param array $associate_data
     * @return $this
     */
    public function where($associate_type, $associate_id, $associate_data = [])
    {
        $this->associate_type = $associate_type;
        $this->associate_id = $associate_id;
        $this->associate_data = $associate_data;

        return $this;
    }

    /**
     * @param $user_event_id
     * @return $this
     * @throws ParamsInvalidException
     */
    public function notice($user_event_id)
    {
        $this->method = 'notice';

        if (!$this->receiver || !is_array($this->receiver)) {
            throw new ParamsInvalidException(['receiver']);
        }

        if (!$user_event_id) {
            throw new ParamsInvalidException(['notify_type']);
        } else {
            $this->user_event_id = $user_event_id;
        }

        if (YII_DEBUG) {
            $this->immediate = true;
        } elseif (empty($this->immediate)) {
            $this->immediate = false;
        }

        #filter to_user_id
        if ($this->filterToUserId()) {
            if ($this->immediate) {
                $result = $this->noticeDatabase();
            } else {
                $result = $this->noticeQueue();
            }
            Yii::trace(sprintf('Notifier::notice Result: %s', var_export($result, true)), 'notifier');

            $this->result[__FUNCTION__] = $result;
        } else {
            Yii::trace('已过滤掉自己给自己发通知', 'notifier');
        }

        return $this;
    }

    public function email($subject = null, $message = null, $template_view = null)
    {
        $this->method = 'email';

        if (empty($subject) && empty($message) && empty ($template_view) && $this->user_event_id) {
            return false;
        }

        #priority = true 为马上执行
        if ($this->immediate) {
            $result = $this->emailSend($this->receiver, $subject, $message, $template_view);
        } else {
            $result = $this->emailQueue($this->receiver, $subject, $message, $template_view);
        }

        Yii::trace(sprintf('Notifier::email Result: %s', var_export($result, true)), 'notifier');

        return $this;
    }

    public function sms($message)
    {
        return $this;
    }

    public function weixin()
    {
        return $this;
    }

    private function filterToUserId()
    {
        #exclude myself
        if (in_array($this->sender, $this->receiver)) {
            $this->receiver = array_diff($this->receiver, [$this->sender]);
        }

        return $this->receiver;
    }

    /**
     * 通知写入数据库
     * @return bool
     */
    public function noticeDatabase()
    {
        return NotificationService::addNotification(
            $this->sender,
            $this->receiver,
            $this->user_event_id,
            $this->associate_type,
            $this->associate_id,
            $this->associate_data,
            TimeHelper::getCurrentTime()
        );
    }


    public function noticeQueue()
    {
        $cache_key = [RedisKey::REDIS_KEY_NOTIFIER, $this->method];
        $this->addSet($this->method);

        return NotificationService::addNotificationToQueue(
            $this->sender,
            $this->receiver,
            $this->user_event_id,
            $this->associate_type,
            $this->associate_id,
            $this->associate_data,
            TimeHelper::getCurrentTime()
        );
    }

    public function emailSend($receivers, $subject, $message, $template_view)
    {
        $mailer = Yii::$app->mailer->compose($template_view);

        foreach ($receivers as $receiver) {
            $result = false;
            if (is_numeric($receiver)) {
                $user = UserService::getUserById($receiver);
                $receiver = $user['email'];
            }

            if (StringHelper::checkEmailFormat($receiver)) {
                $result = $mailer->setTo($receiver)->setSubject($subject)->setTextBody(
                    $message
                )->send();
            }
            $this->result[__FUNCTION__][$receiver] = $result;
        }

        return true;
    }

    public function emailQueue($receiver, $subject, $message, $template_view)
    {
        Yii::trace(
            Json::encode(
                [
                    'receiver'      => $receiver,
                    'subject'       => $subject,
                    'message'       => $message,
                    'template_view' => $template_view,
                ]
            ),
            'notifier'
        );

        $cache_key = [RedisKey::REDIS_KEY_NOTIFIER, $this->method];

        $this->addSet($this->method);

        return Yii::$app->redis->rPush(
            $cache_key,
            [
                'receiver'      => $this->receiver,
                'subject'       => $subject,
                'message'       => $message,
                'template_view' => $template_view,
            ]
        );
    }

    public function popUpQueue($set)
    {
        return Yii::$app->redis->lPop([RedisKey::REDIS_KEY_NOTIFIER, $set]);
    }

    public function getSet()
    {
        return Yii::$app->redis->SMEMBERS([RedisKey::REDIS_KEY_NOTIFIER_SET]);
    }

    private function addSet($set)
    {
        return Yii::$app->redis->sAdd([RedisKey::REDIS_KEY_NOTIFIER_SET], $set);
    }
}
