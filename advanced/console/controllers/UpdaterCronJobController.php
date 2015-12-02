<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/22
 * Time: 11:09
 */

namespace console\controllers;


use common\components\Updater;
use yii\console\Controller;
use Yii;

class UpdaterCronJobController extends Controller
{
    const NUMBER_OF_EACH = 1000;


    public function actionIndex()
    {
        $updater_set = Updater::getSet();

        foreach ($updater_set as $object) {
            //var_dump($item = Updater::popUpQueue($object));exit;
            $i = 0;
            /*var_dump($item = Updater::popUpQueue($object));
            var_dump($i <= self::NUMBER_OF_EACH);
            exit;*/
            echo sprintf('Process Table %s', $object), PHP_EOL;
            while ($i <= self::NUMBER_OF_EACH && $item = Updater::popUpQueue($object)) {
                $result = Updater::writeToDatabase($item['table'], $item['set'], $item['where']);

                if (!$result) {
                    //when update nothing, nothing to do
                    //Updater::pushToQueue($item['table'], $item['set'], $item['where']);
                    echo sprintf('Table %s, no affected!', $object), PHP_EOL;
                } else {
                    echo sprintf('Table %s ', $object) . 'Success', PHP_EOL;
                }

                $i++;
            }
            echo sprintf('Done Table %s', $object), PHP_EOL, PHP_EOL;
        }
    }
}
