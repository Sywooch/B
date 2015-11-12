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

    public function notice($type, $associate_id = null)
    {
        $this->method = 'notice';

        if (!$this->receiver || !is_array($this->receiver)) {
            throw new ParamsInvalidException(['user_ids']);
        }

        if (!$type) {
            throw new ParamsInvalidException(['notify_type']);
        }

        #filter to_user_id
        if ($this->filterToUserId()) {
            #priority = true 为马上执行
            if ($this->priority) {
                $result = $this->noticeImmediately($type, $associate_id);
            } else {
                $result = $this->noticeQueue($type, $associate_id);
            }
            Yii::trace(sprintf('Notifier::notice Result: %s', var_export($result, true)), 'notifier');

            $this->result[__FUNCTION__] = $result;
        }

        return $this;
    }

    public function email($subject, $message = null, $template_view = null)
    {
        #priority = true 为马上执行
        if ($this->priority) {
            $result = $this->emailImmediately($subject, $message, $template_view);
        } else {
            $result = $this->emailQueue($subject, $message, $template_view);
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

    /**
     * @param string|array $method 'notice,sms,email,weixin'
     */
    public function send($method = 'notice')
    {
        if (is_string($method)) {
            $method = explode(',', $method);
        }

        foreach ($method as $item) {
            $this->method = $item;
            $this->$item();
        }
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
    
    private function noticeImmediately($type, $associate_id)
    {
        /* @var $notificationEntity NotificationEntity */
        $notificationEntity = Yii::createObject(NotificationEntity::className());

        return $notificationEntity->addNotify(
            $this->sender,
            $this->receiver,
            $type,
            $associate_id,
            TimeHelper::getCurrentTime()
        );
    }
    
    private function noticeQueue($type, $associate_id)
    {
        Yii::trace(
            [
                'from_user_id' => $this->sender,
                'to_user_id'   => $this->receiver,
                'type'         => $type,
                'associate_id' => $associate_id,
                'status'       => NotificationEntity::STATUS_UNREAD,
                'create_at'    => TimeHelper::getCurrentTime(),
            ],
            'notifier'
        );

        $cache_key = [REDIS_KEY_NOTIFIER, implode(':', [$this->method, $type])];

        self::addSet($this->method, $type);

        return Yii::$app->redis->rPush(
            $cache_key,
            [
                'from_user_id' => $this->sender,
                'to_user_id'   => $this->receiver,
                'type'         => $type,
                'associate_id' => $associate_id,
                'status'       => NotificationEntity::STATUS_UNREAD,
                'create_at'    => TimeHelper::getCurrentTime(),
            ]
        );
    }

    private function emailImmediately($subject, $message, $template_view)
    {
        $mailer = Yii::$app->mailer->compose($template_view);

        foreach ($this->receiver as $receiver) {
            $result = false;
            if (is_numeric($receiver)) {
                $user = UserEntity::getUserById($this->sender);
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

    private function emailQueue($subject, $message, $template_view)
    {
        Yii::trace(
            [
                'from_user_id' => $this->sender,
                'to_user_id'   => $this->receiver,
                'type'         => $type,
                'associate_id' => $associate_id,
                'status'       => NotificationEntity::STATUS_UNREAD,
                'create_at'    => TimeHelper::getCurrentTime(),
            ],
            'notifier'
        );

        $cache_key = [REDIS_KEY_NOTIFIER, implode(':', [$this->method, $type])];

        self::addSet($this->method, $type);

        return Yii::$app->redis->rPush(
            $cache_key,
            [
                'from_user_id' => $this->sender,
                'to_user_id'   => $this->receiver,
                'type'         => $type,
                'associate_id' => $associate_id,
                'status'       => NotificationEntity::STATUS_UNREAD,
                'create_at'    => TimeHelper::getCurrentTime(),
            ]
        );
    }

    public static function popUpQueue($table)
    {
        return Yii::$app->redis->lPop([REDIS_KEY_NOTIFIER, $table]);
    }

    public static function getSet()
    {
        return Yii::$app->redis->SMEMBERS([REDIS_KEY_NOTIFIER_SET]);
    }

    private static function addSet($method, $table)
    {
        return Yii::$app->redis->sAdd([REDIS_KEY_NOTIFIER_SET], implode(':', [$method, $table]));;
    }
}