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

class MailerCronJobController extends Controller
{
    const SERVER = 'imap.exmail.qq.com';
    const USERNAME = 'admin@bo-u.cn';
    const PASSWORD = '662800Yu';

    public function actionIndex()
    {
        $this->receive();
        $this->dealWithQueue();
    }

    public function receive()
    {
        $mailbox = new \PhpImap\Mailbox(sprintf('{%s}INBOX', self::SERVER), self::USERNAME, self::PASSWORD, __DIR__);

        $mail_ids = $mailbox->searchMailBox('UNSEEN');
        if (!$mail_ids) {
            die('Mailbox is empty');
        }

        $mails = $mailbox->getMailsInfo($mail_ids);

        foreach($mails as $mail){
            print_r($mail);
        }

        //set read
        $mailbox->setFlag($mail_ids, '\\Seen');
    }

    private function consumeQueue()
    {

    }

    private function pushToQueue()
    {

    }


    /*private function decodeSubject($subject)
    {

        $data = imap_mime_header_decode($subject);

        $new_subject = [];
        foreach ($data as $item) {
            if ($item->charset != 'default' && $item->charset != 'UTF-8') {
                $item->text = mb_convert_encoding($item->text, 'utf8', $item->charset);
            }
            $new_subject[] = $item->text;
        }

        return implode('', $new_subject);
    }*/

}