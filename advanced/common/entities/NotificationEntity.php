<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/20
 * Time: 11:32
 */

namespace common\entities;


use common\behaviors\NotificationBehavior;
use common\models\Notification;
use common\services\UserService;
use Yii;
use yii\db\ActiveRecord;

class NotificationEntity extends Notification
{
    const STATUS_UNREAD = 'unread';
    const STATUS_READ = 'read';
    
    
    const TYPE_SYSTEM = 100;
    
    const TYPE_ANSWER_AT_ME = 200;
    const TYPE_COMMENT_AT_ME = 201;
    const TYPE_ANSWER_COMMENT_TO_ME = 202;
    const TYPE_FOLLOW_ME = 203;
    const TYPE_FOLLOW_MY_SPECIAL_COLUMN = 204;
    const TYPE_MESSAGE_TO_ME = 205;
    const TYPE_INVITE_ME_TO_ANSWER = 206;

    const TYPE_MY_QUESTION_IS_MODIFIED = 207;
    const TYPE_MY_QUESTION_IS_LOCK = 208;
    const TYPE_MY_QUESTION_IS_CLOSE = 209;

    const TYPE_MY_ANSWER_IS_AGREED = 210;
    const TYPE_MY_ANSWER_IS_MODIFIED = 211;
    const TYPE_MY_ANSWER_IS_FOLD = 212;
    const TYPE_MY_ANSWER_HAS_NEW_COMMENT = 213;
    
    const TYPE_FOLLOW_QUESTION_HAS_NEW_ANSWER = 300;
    const TYPE_FOLLOW_QUESTION_MODIFY_ANSWER = 301;
    const TYPE_FOLLOW_TAS_HAS_NEW_QUESTION = 302;
    const TYPE_FOLLOW_FAVORITE_HAS_NEW_QUESTION = 303;

    const TYPE_PM_RECEIVE = 400;

    /*public function behaviors()
    {
        return [
            'question_behavior' => [
                'class' => NotificationBehavior::className(),
            ],
        ];
    }*/

    public function addNotify($from_user_id, array $to_user_id, $type, $associate_id, $create_at)
    {
        $data = [];

        foreach ($to_user_id as $user_id) {
            $data[] = [
                'from_user_id' => $from_user_id,
                'to_user_id'   => $user_id,
                'type'         => $type,
                'associate_id' => $associate_id,
                'status'       => self::STATUS_UNREAD,
                'create_at'    => $create_at,
            ];
        }

        $command = self::getDb()->createCommand()->batchInsert(
            self::tableName(),
            [
                'from_user_id',
                'to_user_id',
                'type',
                'associate_id',
                'status',
                'create_at',
            ],
            $data
        );

        //echo $command->getSql();
        if ($command->execute()) {

            #increase count_notification BY trigger
            //$this->trigger(ActiveRecord::EVENT_AFTER_INSERT);

            /* @var $userService UserService */
            $userService = Yii::createObject(UserService::className());
            $userService->increaseNotificationCount($to_user_id);


            return true;
        } else {
            Yii::error(
                sprintf(
                    'INSERT %s Fail, SQL: %s',
                    NotificationEntity::tableName(),
                    $command->getSql()
                ),
                'notifier'
            );

            return false;
        }
    }


}