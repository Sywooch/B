<?php
/**
 * 每15分钟执行一次
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/22
 * Time: 15:57
 */

namespace console\controllers;


use common\components\Notifier;
use common\entities\UserEntity;
use common\models\UserInteractiveMailLog;
use fedemotta\cronjob\models\CronJob;
use Yii;
use yii\console\Controller;

class MailerCronJobController extends Controller
{
    const SERVER = 'imap.exmail.qq.com';
    const USERNAME = 'admin@bo-u.cn';
    const PASSWORD = '662800Yu';
    const MAX_NUMBER_PER_LOOP = 100;

    public function actionIndex()
    {
        $this->consumeQueue();
        $this->produce();
        $this->consumeQueue();
    }

    private function produce()
    {
        set_time_limit(900);

        $mailbox = new \PhpImap\Mailbox(sprintf('{%s}INBOX', self::SERVER), self::USERNAME, self::PASSWORD, __DIR__);

        $mail_ids = $mailbox->searchMailBox('UNSEEN');

        if ($mail_ids) {
            $mails = $mailbox->getMailsInfo($mail_ids);

            /*stdClass Object
            (
                [subject] => 111
                [from] => 967 <6202551@qq.com>
                [to] => admin <admin@bo-u.cn>
                [date] => Wed, 4 Nov 2015 09:45:07 +0800
                [message_id] => <tencent_76BD3E0F261C573B6E6038FE@qq.com>
                [size] => 1271
                [uid] => 124
                [msgno] => 8
                [recent] => 0
                [flagged] => 0
                [answered] => 0
                [deleted] => 0
                [seen] => 0
                [draft] => 0
                [udate] => 1446601508
            )*/

            $sender = [];
            foreach ($mails as $mail) {
                if (preg_match('/<(.+?)>/i', $mail->from, $from)) {
                    $sender[] = $from[1];
                }
            }
            if ($sender) {
                $this->pushToQueue($sender);
            }
            //set read
            $mailbox->setFlag($mail_ids, '\\Seen');
        } else {
            echo '没有要处理的邮件。', PHP_EOL;
        }
    }

    private function consumeQueue()
    {
        $i = 0;
        $mail = Yii::$app->redis->sPop([REDIS_KEY_EMAIL, 'register_active']);
        while ($i <= self::MAX_NUMBER_PER_LOOP && $mail) {
            $this->deal($mail);
            $mail = Yii::$app->redis->sPop([REDIS_KEY_EMAIL, 'register_active']);
            $i++;
        }

        if ($i) {
            echo sprintf('处理完成,共处理 %d 邮件。', $i), PHP_EOL;
        }
    }

    private function deal($mail)
    {
        echo sprintf('开始处理邮件：%s', $mail), PHP_EOL;
        /* @var $user UserEntity */
        $user = UserEntity::find()->select(['id', 'confirmed_at'])->where(
            ['email' => $mail]
        )->one();

        if ($user) {

            if (!$user->confirmed_at) {
                $user->confirmed_at = time();
                $sql = sprintf('update `%s` set confirmed_at=:confirmed_at WHERE id=:id', UserEntity::tableName());
                $command = $user->getDb()->createCommand(
                    $sql,
                    [
                        ':confirmed_at' => time(),
                        ':id'           => $user->id,
                    ]
                );

                if (!$command->execute()) {
                    echo sprintf('用户%s激活失败', $user->username), PHP_EOL;
                }
            }

            if (preg_match('/qq\.com/i', $mail, $is_qq_mail)) {
                $user_interactive_mail_log = UserInteractiveMailLog::find()->where(
                    [
                        'user_Id' => $user->id,
                    ]
                )->one();
                if (!$user_interactive_mail_log) {
                    $user_interactive_mail_log = new UserInteractiveMailLog;
                    $user_interactive_mail_log->user_id = $user->id;
                }

                $user_interactive_mail_log->email = $mail;
                $result = $user_interactive_mail_log->save();

                if (!$result) {
                    echo '未成功保存到 user_has_qq_mail', PHP_EOL;
                }
            }
        }
    }

    private function pushToQueue(array $mail_address)
    {
        $params = array_merge(
            [
                [REDIS_KEY_EMAIL, 'register_active'],

            ],
            array_values($mail_address)
        );

        return call_user_func_array(
            [Yii::$app->redis, 'sAdd'],
            $params
        );
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