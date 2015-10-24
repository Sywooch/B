<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/20
 * Time: 10:46
 */

namespace common\components;


use common\entities\NotificationEntity;
use common\exceptions\ParamsInvalidException;
use Yii;
use yii\base\Object;

class Notifier extends Object
{
    private static $instance;
    private $priority; #优先级, true 立马执行　false 队列
    private $from_user_id; #谁发送
    private $to_user_id; #发送给谁
    private $type; #通知类型
    private $associate_id; #通知内容的参数
    public static $allow_notify_type = [
    ];
    
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
        $this->from_user_id = null;
        $this->to_user_id = null;
        $this->type = null;
    }
    
    public function priority($priority = true)
    {
        $this->priority = $priority;

        return $this;
    }
    
    public function from($user_id = null)
    {
        $this->from_user_id = $user_id;

        return $this;
    }
    
    
    public function to($user_id)
    {
        $user_ids = [];
        
        if (is_array($user_id)) {
            $user_ids = $user_id;
        } elseif (is_numeric($user_id)) {
            $user_ids[] = $user_id;
        } elseif (count(explode(',', $user_id))) {
            $user_ids = explode(',', $user_id);
        }
        
        $this->to_user_id = $user_ids;

        return $this;
    }

    /**
     * @param      $type
     * @param null $associate_id
     * @return $this
     */
    public function set($type, $associate_id = null)
    {
        $this->type = $type;
        $this->associate_id = $associate_id;

        return $this;
    }
    
    public function send()
    {
        if (!$this->to_user_id || !is_array($this->to_user_id)) {
            throw new ParamsInvalidException(['user_ids']);
        }
        
        if (!$this->type) {
            throw new ParamsInvalidException(['notify_type']);
        }

        #filter to_user_id
        if ($this->filterToUserId()) {
            #priority = true 为马上执行
            if ($this->priority) {
                return $this->immediately();
            } else {
                return $this->simpleQueue();
            }
        }
    }

    public function test()
    {
        echo '<pre />';
        print_r(
            [
                'from_user_id' => $this->from_user_id,
                'to_user_id'   => $this->to_user_id,
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
        if (in_array($this->from_user_id, $this->to_user_id)) {
            $this->to_user_id = array_diff($this->to_user_id, [$this->from_user_id]);
        }

        return $this->to_user_id;
    }
    
    private function immediately()
    {
        $create_at = time();

        return self::writeToDatabase(
            $this->from_user_id,
            $this->to_user_id,
            $this->type,
            $this->associate_id,
            $create_at
        );
    }

    public static function writeToDatabase(
        $from_user_id,
        $to_user_id,
        $type,
        $associate_id,
        $create_at
    ) {
        /* @var $notificationEntity NotificationEntity */
        $notificationEntity = Yii::createObject(NotificationEntity::className());

        return $notificationEntity->addNotify($from_user_id, $to_user_id, $type, $associate_id, $create_at);

    }
    
    private function simpleQueue()
    {
        $create_at = time();
        Yii::trace(
            [
                'from_user_id' => $this->from_user_id,
                'to_user_id'   => $this->to_user_id,
                'type'         => $this->type,
                'associate_id' => $this->associate_id,
                'status'       => NotificationEntity::STATUS_UNREAD,
                'create_at'    => $create_at,
            ],
            'notifier'
        );
        
        return self::pushToQueue(
            $this->from_user_id,
            $this->to_user_id,
            $this->type,
            $this->associate_id,
            NotificationEntity::STATUS_UNREAD,
            $create_at
        );
    }
    
    public static function pushToQueue(
        $from_user_id,
        $to_user_id,
        $type,
        $associate_id,
        $status,
        $create_at
    ) {

        self::addSet($type);

        return Yii::$app->redis->rPush(
            [REDIS_KEY_NOTIFIER, $type],
            [
                'from_user_id' => $from_user_id,
                'to_user_id'   => $to_user_id,
                'type'         => $type,
                'associate_id' => $associate_id,
                'status'       => $status,
                'create_at'    => $create_at,
            ]
        );
    }

    public static function popUpQueue($table)
    {
        return Yii::$app->redis->lPop([REDIS_KEY_NOTIFIER, $table]);
    }

    public static function getSet()
    {
        return Yii::$app->redis->SMEMBERS(REDIS_KEY_NOTIFIER_SET);
    }

    private static function addSet($table)
    {
        return Yii::$app->redis->sAdd(REDIS_KEY_NOTIFIER_SET, $table);;
    }
}