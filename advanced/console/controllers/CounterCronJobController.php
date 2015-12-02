<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 14:58
 */

namespace console\controllers;

use common\components\Counter;
use Yii;
use yii\console\Controller;

class CounterCronJobController extends Controller
{
    const NUMBER_OF_EACH = 1000;

    public function actionIndex()
    {
        $counter_set = Counter::getSet();

        foreach ($counter_set as $object) {
            echo sprintf('Process Table %s ', $object), PHP_EOL;
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
            echo sprintf('Done Table %s ', $object), PHP_EOL, PHP_EOL;
        }
    }
}
