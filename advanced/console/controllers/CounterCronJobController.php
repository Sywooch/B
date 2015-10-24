<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 14:58
 */

namespace console\controllers;


use common\components\Counter;
use fedemotta\cronjob\models\CronJob;
use Yii;
use yii\console\Controller;

class CounterCronJobController extends Controller
{
    const NUMBER_OF_EACH = 1000;

    /**
     * Run SomeModel::some_method for a period of time
     * @param string $from
     * @param string $to
     * @return int exit code
     */
    public function actionInit($from, $to)
    {
        $dates = CronJob::getDateRange($from, $to);
        $command = CronJob::run($this->id, $this->action->id, 0, CronJob::countDateRange($dates));
        if ($command === false) {
            return Controller::EXIT_CODE_ERROR;
        } else {
            foreach ($dates as $date) {
                $this->consume();
                
            }
            $command->finish();
            
            return Controller::EXIT_CODE_NORMAL;
        }
    }
    
    private function consume()
    {
        $counter_set = Counter::getSet();

        foreach ($counter_set as $object) {

            $i = 0;
            while ($i <= self::NUMBER_OF_EACH && $item = Counter::popUpQueue($object)) {

                $table = $item['table'];
                $primary_key_name = $item['primary_key_name'];
                $field = $item['field'];
                $value = $item['value'];
                $id = $item['id'];

                $result = Counter::writeToDatabase($table, $primary_key_name, $id, $field, $value);

                if (!$result) {
                    Counter::pushToQueue($table, $primary_key_name, $id, $field, $value);
                } else {
                    echo sprintf('Table %s ', $object) . 'Success', PHP_EOL;
                }

                $i++;
            }
        }
    }



    /*
     * 以下是合并插入
     * private function consume()
    {
        $counter_data = [];
        foreach (Counter::$allow_tables as $object) {

            $data = \Yii::$app->redis->lRange(['counter', $object], 0, 1000);
            foreach ($data as $item) {

                $table = $item['table'];
                $primary_key_name = $item['primary_key_name'];
                $field = $item['field'];
                $value = $item['value'];
                $id = $item['id'];

                if (!isset($counter_data[$table]['primary_key_name'])) {
                    $counter_data[$table]['primary_key_name'] = $primary_key_name;
                }

                $counter_data[$table]['data'][$field][$value][] = $id;
            }
        }

        $this->process($counter_data);
    }

    private function process($counter_data)
    {
        $sql = [];
        foreach ($counter_data as $table_name => $counter) {
            $primary_key_name = $counter['primary_key_name'];

            foreach ($counter['data'] as $field => $values) {

                foreach ($values as $value => $id) {
                    $sql[] = sprintf(
                        "UPDATE %s SET %s=%s+%s WHERE %s IN(%s);",
                        $table_name,
                        $field,
                        $field,
                        $value,
                        $primary_key_name,
                        "'" . implode(",'") . "'"
                    );
                }
            }
        }

        print_r($sql);

        $command = \Yii::$app->db->createCommand(implode('', $sql));
        $command->execute();
    }*/
    
    /**
     * Run SomeModel::some_method for today only as the default action
     * @return int exit code
     */
    public function actionIndex()
    {
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));
    }
    
    /**
     * Run SomeModel::some_method for yesterday
     * @return int exit code
     */
    public function actionYesterday()
    {
        return $this->actionInit(date("Y-m-d", strtotime("-1 days")), date("Y-m-d", strtotime("-1 days")));
    }
}