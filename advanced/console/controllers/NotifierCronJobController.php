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
use Yii;
use yii\console\Controller;

class NotifierCronJobController extends Controller
{
    const NUMBER_OF_EACH = 1000;

    public function actionIndex()
    {
        /* @var $notifier Notifier */
        $notifier = Notifier::build();

        $notifier_set = $notifier->getSet();

        foreach ($notifier_set as $method) {
            $i = 0;
            while ($i <= self::NUMBER_OF_EACH &&
                $item = $notifier->popUpQueue($method)) {
                switch ($method) {
                    case 'notice':
                        $result = $notifier->sync(true)->from($item['sender'])
                                           ->to($item['receiver'])
                                           ->where(
                                               $item['associate_type'],
                                               $item['associate_id'],
                                               $item['associate_data']
                                           )
                                           ->notice($item['notice_code']);


                        //消费失败，插入队列
                        if (!$result) {
                            $notifier->sync(false)->from($item['sender'])
                                     ->to($item['receiver'])
                                     ->where(
                                         $item['associate_type'],
                                         $item['associate_id'],
                                         $item['associate_data']
                                     )
                                     ->notice($item['notice_code']);
                        } else {
                            echo sprintf('Notification Notice Code:  %s ', $item['notice_code']) . 'Success', PHP_EOL;
                        }

                        break;


                    case 'email':
                        $result = $notifier->emailSend(
                            $item['receiver'],
                            $item['subject'],
                            $item['message'],
                            $item['template_view']
                        );

                        if (!$result) {
                            //when update nothing, nothing to do
                            $notifier->emailQueue(
                                $item['receiver'],
                                $item['subject'],
                                $item['message'],
                                $item['template_view']
                            );
                            //echo sprintf('Notification Type:  %s, no affected!', $object), PHP_EOL;
                        } else {
                            echo
                                sprintf(
                                    'Notification Email:  %s ',
                                    var_export($item['receiver'], true)
                                ) . 'Success', PHP_EOL;
                        }
                        break;
                }
                $i++;
            }
        }
    }

}