<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/20
 * Time: 10:46
 */

namespace common\components;


use common\entities\NotificationEntity;
use common\entities\UserEntity;
use common\exceptions\ParamsInvalidException;
use common\helpers\StringHelper;
use common\helpers\TimeHelper;
use Yii;
use yii\base\Object;

class BaseNotifier extends Object
{

    const METHOD_NOTICE = 'notice';
    const METHOD_EMAIL = 'email';
    const METHOD_SMS = 'sms';
    const METHOD_WEIXIN = 'weixin';

    public $result; #结果集
    private static $instance;
    private $priority; #优先级, true 立马执行　false 队列
    private $sender; #谁发送
    private $receiver; #发送给谁
    private $method; #通知方法:notice,email,sms,weixin

    private $notice_code, $notice_message, $notice_associate_id;


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
        $this->priority = false;
        $this->sender = null;
        $this->receiver = null;
        $this->result = null;
        $this->method = null;
    }
    
    public function priority($priority = true)
    {
        $this->priority = $priority;

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

    public function notice($type, $associate_data = null)
    {
        $this->method = 'notice';

        if (!$this->receiver || !is_array($this->receiver)) {
            throw new ParamsInvalidException(['user_ids']);
        }

        if (!$type) {
            throw new ParamsInvalidException(['notify_type']);
        } else {
            $this->notice_code = NotificationEntity::getNotificationCode($type, $associate_data);
        }

        #filter to_user_id
        if ($this->filterToUserId()) {
            #priority = true 为马上执行
            if ($this->priority) {
                $result = $this->noticeDatabase($this->sender, $this->receiver, $this->notice_code, $associate_data);
            } else {
                $result = $this->noticeQueue($this->sender, $this->receiver, $this->notice_code, $associate_data);
            }
            Yii::trace(sprintf('Notifier::notice Result: %s', var_export($result, true)), 'notifier');

            $this->result[__FUNCTION__] = $result;
        }

        return $this;
    }

    public function email($subject = null, $message = null, $template_view = null)
    {
        $this->method = 'email';

        if (empty($subject) && empty($message) && empty ($template_view) && $this->notice_code) {

        }

        #priority = true 为马上执行
        if ($this->priority) {
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

    public function test()
    {
        echo '<pre />';
        print_r(
            [
                'from_user_id' => $this->sender,
                'to_user_id'   => $this->receiver,
                'type'         => $this->type,
                'associate_id' => $this->associate_id,
                'status'       => NotificationEntity::STATUS_UNREAD,
            ]
        );
        Yii::$app->end();
    }
    
    private function filterToUserId()
    {
        #exclude myself
        if (in_array($this->sender, $this->receiver)) {
            $this->receiver = array_diff($this->receiver, [$this->sender]);
        }

        return $this->receiver;
    }
    
    public function noticeDatabase($sender, $receiver, $notice_code, $associate_data, $current_time = null)
    {
        return NotificationEntity::addNotify(
            $sender,
            $receiver,
            $notice_code,
            $associate_data,
            $current_time ? $current_time : TimeHelper::getCurrentTime()
        );
    }


    public function noticeQueue($sender, $receiver, $notice_code, $associate_data, $current_time = null)
    {
        Yii::trace(
            [
                'sender'         => $sender,
                'receiver'       => $receiver,
                'notice_code'    => $notice_code,
                'associate_data' => $associate_data,
                'status'         => NotificationEntity::STATUS_UNREAD,
                'create_at'      => $current_time ? $current_time : TimeHelper::getCurrentTime(),
            ],
            'notifier'
        );

        $cache_key = [REDIS_KEY_NOTIFIER, implode(':', [$this->method, $notice_code])];

        $this->addSet($this->method, $notice_code);

        return Yii::$app->redis->rPush(
            $cache_key,
            [
                'sender'         => $sender,
                'receiver'       => $receiver,
                'notice_code'    => $notice_code,
                'associate_data' => $associate_data,
                'status'         => NotificationEntity::STATUS_UNREAD,
                'create_at'      => $current_time ? $current_time : TimeHelper::getCurrentTime(),
            ]
        );
    }

    public function emailSend($receivers, $subject, $message, $template_view)
    {
        $mailer = Yii::$app->mailer->compose($template_view);

        foreach ($receivers as $receiver) {
            $result = false;
            if (is_numeric($receiver)) {
                $user = UserEntity::getUserById($receiver);
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
            [
                'receiver'      => $receiver,
                'subject'       => $subject,
                'message'       => $message,
                'template_view' => $template_view,
            ],
            'notifier'
        );

        $cache_key = [REDIS_KEY_NOTIFIER, $this->method];

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

    public function popUpQueue($method)
    {
        return Yii::$app->redis->lPop([REDIS_KEY_NOTIFIER, $method]);
    }

    public function getSet()
    {
        return Yii::$app->redis->SMEMBERS([REDIS_KEY_NOTIFIER_SET]);
    }

    private function addSet($method)
    {
        return Yii::$app->redis->sAdd([REDIS_KEY_NOTIFIER_SET], $method);;
    }
}