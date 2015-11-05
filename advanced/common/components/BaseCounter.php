<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 10:27
 */

namespace common\components;


use common\entities\FavoriteEntity;
use common\entities\PrivateMessageEntity;
use common\entities\QuestionEntity;
use common\entities\UserProfileEntity;
use common\exceptions\ParamsInvalidException;
use Yii;
use yii\base\Object;

class BaseCounter extends Object
{
    private $table; #表名
    private $primary_key_name = 'id'; #主键名称
    private $id; #
    private $field; #修改的属性
    private $value; #修改的值
    private $priority; #优先级, true 立马执行　false 队列

    private static $instance;
    

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
        $this->table = null;
        $this->primary_key_name = 'id';
        $this->id = null;
        $this->field = null;
        $this->value = null;
        $this->priority = false;
    }

    public function set($table_name, $id, $primary_key_name = 'id')
    {
        $this->table = $table_name;
        $this->primary_key_name = $primary_key_name;
        $this->id = $id;

        return $this;
    }
    
    public function value($field, $value = 1)
    {
        $this->field = $field;
        $this->value = $value;

        return $this;
    }

    public function multiple($multiple)
    {
        if ($multiple > 1) {
            $this->value = $this->value * $multiple;
        }

        return $this;
    }

    public function priority($priority = true)
    {
        $this->priority = $priority;
        
        return $this;
    }

    public function test()
    {
        echo '<pre />';
        print_r(
            [
                'table'            => $this->table,
                'primary_key_name' => $this->primary_key_name,
                'id'               => $this->id,
                'field'            => $this->field,
                'value'            => $this->value,
            ]
        );
        Yii::$app->end();
    }
    
    public function execute()
    {
        if (empty($this->table) || empty($this->id)) {
            throw new ParamsInvalidException(['object']);
        }
        
        if (empty($this->field)) {
            throw new ParamsInvalidException(['attribute']);
        }
        if (empty($this->value)) {
            throw new ParamsInvalidException(['value']);
        }
        
        if (empty($this->priority)) {
            $this->priority = false;
        }

        #priority = true 为马上执行
        if ($this->priority) {
            $result = $this->immediately();
        } else {
            $result = $this->simpleQueue();
        }

        Yii::trace(sprintf('Counter Result %s', var_export($result, true)), 'counter');

        return $result;
    }
    
    private function immediately()
    {
        Yii::trace(
            ',',
            [
                'table'            => $this->table,
                'primary_key_name' => $this->primary_key_name,
                'id'               => $this->id,
                'field'            => $this->field,
                'value'            => $this->value,
            ],
            'counter'
        );

        return self::writeToDatabase($this->table, $this->primary_key_name, $this->id, $this->field, $this->value);
    }

    public static function writeToDatabase($table, $primary_key_name, $id, $field, $value)
    {
        $sql = sprintf(
            'UPDATE %s SET %s=%s+:value WHERE %s=:id',
            $table,
            $field,
            $field,
            $primary_key_name
        );
        $db = Yii::$app->db;

        //print_r($db->schema);exit;
        $command = $db->createCommand(
            $sql,
            [
                ':id'    => intval($id),
                ':value' => $value,
            ]
        );

        if ($command->execute()) {
            return true;
        } else {
            Yii::error(sprintf('UPDATE COUNTER FAIL, SQL: %s', $command->getSql()), 'counter');

            return false;
        }
    }
    
    private function simpleQueue()
    {
        Yii::trace(
            [
                'table'            => $this->table,
                'primary_key_name' => $this->primary_key_name,
                'id'               => $this->id,
                'field'            => $this->field,
                'value'            => $this->value,
            ],
            'counter'
        );

        return self::pushToQueue($this->table, $this->primary_key_name, $this->id, $this->field, $this->value);
    }

    public static function pushToQueue($table, $primary_key_name, $id, $field, $value)
    {
        self::addSet($table);

        return Yii::$app->redis->rPush(
            [REDIS_KEY_COUNTER, $table],
            [
                'table'            => $table,
                'primary_key_name' => $primary_key_name,
                'id'               => $id,
                'field'            => $field,
                'value'            => $value,
            ]
        );
    }


    public static function popUpQueue($table)
    {
        return Yii::$app->redis->lPop([REDIS_KEY_COUNTER, $table]);
    }

    public static function getSet()
    {
        return Yii::$app->redis->SMEMBERS([REDIS_KEY_COUNTER_SET]);
    }

    private static function addSet($table)
    {
        return Yii::$app->redis->sAdd([REDIS_KEY_COUNTER_SET], $table);;
    }


}