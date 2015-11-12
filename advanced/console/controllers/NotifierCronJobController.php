<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/22
 * Time: 15:57
 */

namespace console\controllers;


use common\components\Notifier;
use fedemotta\cronjob\models\CronJob;
use yii\console\Controller;

class NotifierCronJobController extends Controller
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
                $this->consumeNotice();

            }
            $command->finish();

            return Controller::EXIT_CODE_NORMAL;
        }
    }

    private function consumeNotice()
    {
        $notifier_set = Notifier::getSet();


        foreach ($notifier_set as $object) {
            $i = 0;
            while ($i <= self::NUMBER_OF_EACH && $item = Notifier::popUpQueue($object)) {

                $result = Notifier::writeToDatabase(
                    $item['from_user_id'],
                    $item['to_user_id'],
                    $item['type'],
                    $item['associate_id'],
                    $item['create_at']
                );

                if (!$result) {
                    //when update nothing, nothing to do
                    Notifier::pushToQueue(
                        $item['from_user_id'],
                        $item['to_user_id'],
                        $item['type'],
                        $item['associate_id'],
                        $item['create_at']
                    );
                    //echo sprintf('Notification Type:  %s, no affected!', $object), PHP_EOL;
                } else {
                    echo sprintf('Notification Type:  %s ', $object) . 'Success', PHP_EOL;
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