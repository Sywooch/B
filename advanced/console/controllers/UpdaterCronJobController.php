<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/22
 * Time: 11:09
 */

namespace console\controllers;


use common\components\Updater;
use fedemotta\cronjob\models\CronJob;
use yii\console\Controller;
use Yii;

class UpdaterCronJobController extends Controller
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
        $updater_set = Updater::getSet();


        foreach ($updater_set as $object) {

            //var_dump($item = Updater::popUpQueue($object));exit;
            $i = 0;
            /*var_dump($item = Updater::popUpQueue($object));
            var_dump($i <= self::NUMBER_OF_EACH);
            exit;*/

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
        }
    }

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