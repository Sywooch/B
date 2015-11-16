<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/22
 * Time: 9:45
 */

namespace common\components;


use Yii;
use yii\base\Object;

class BaseUpdater extends Object
{
    private static $instance;
    private $immediate;
    private $table;
    private $where;
    private $set;

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
        $this->set = null;
        $this->where = null;
    }

    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    public function where(array $where)
    {
        $this->where = $where;

        return $this;
    }

    public function set(array $data)
    {
        $this->set = $data;

        return $this;
    }

    public function sync($immediate = true)
    {
        $this->immediate = $immediate;

        return $this;
    }

    public function execute()
    {
        if (empty($this->table)) {
            throw new ParamsInvalidException(['table']);
        }

        if (empty($this->where) || !is_array($this->where)) {
            throw new ParamsInvalidException(['where']);
        }

        if (empty($this->set) || !is_array($this->set)) {
            throw new ParamsInvalidException(['set']);
        }

        #priority = true 为马上执行
        if ($this->immediate) {
            $result =  $this->immediately();
        } else {
            $result =  $this->simpleQueue();
        }

        Yii::trace(sprintf('Updater Result %s', var_export($result, true)), 'counter');

        return $result;
    }

    public function test()
    {
        echo '<pre />';
        print_r(
            [
                'table' => $this->table,
                'set'   => $this->set,
                'where' => $this->where,
            ]
        );
        Yii::$app->end();
    }

    private function immediately()
    {
        Yii::trace(
            ',',
            [
                'table' => $this->table,
                'set'   => $this->set,
                'where' => $this->where,
            ],
            'updater'
        );

        return self::writeToDatabase($this->table, $this->set, $this->where);
    }

    public static function writeToDatabase($table, $set, $where)
    {
        $set_data = $where_data = [];

        foreach ($set as $field => $value) {
            $set_data[] = sprintf('`%s`="%s"', $field, $value);
        }

        foreach ($where as $field => $value) {
            $where_data[] = sprintf('`%s`="%s"', $field, $value);
        }

        $sql = sprintf('UPDATE `%s` SET %s WHERE %s', $table, implode(' AND ', $set_data), implode(' AND ', $where_data));

        //print_r($sql);exit;

        $command = Yii::$app->getDb()->createCommand($sql);


        if ($command->execute()) {
            $result = true;
        } else {
            $result = false;
            Yii::trace(sprintf('Fail After update active at, SQL:%s', 'updater', $command->getRawSql()));
        }

        return $result;
    }

    private function simpleQueue()
    {
        Yii::trace(
            [
                'table' => $this->table,
                'set'   => $this->set,
                'where' => $this->where,
            ],
            'updater'
        );

        return self::pushToQueue($this->table, $this->set, $this->where);
    }

    public static function pushToQueue($table, $set, $where)
    {
        self::addSet($table);

        return Yii::$app->redis->rPush(
            [REDIS_KEY_UPDATER, $table],
            [
                'table' => $table,
                'set'   => $set,
                'where' => $where,
            ]
        );
    }


    public static function popUpQueue($table)
    {
        return Yii::$app->redis->lPop([REDIS_KEY_UPDATER, $table]);
    }

    public static function getSet()
    {
        return Yii::$app->redis->SMEMBERS(REDIS_KEY_UPDATER_SET);
    }

    private static function addSet($table)
    {
        return Yii::$app->redis->sAdd([REDIS_KEY_UPDATER_SET], $table);;
    }
}