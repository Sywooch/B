<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/23
 * Time: 10:05
 */

namespace common\entities;


use common\behaviors\IpBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\PrivateMessageDialogBehavior;
use common\behaviors\TimestampBehavior;
use common\models\PrivateMessageDialog;
use Yii;
use yii\db\ActiveRecord;

class PrivateMessageDialogEntity extends PrivateMessageDialog
{

    const STATUS_READ = 'read';
    const STATUS_UNREAD = 'unread';

    public function behaviors()
    {
        return [
            'operator'                        => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                ],
            ],
            'timestamp'                       => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],
            'ip_behavior'                     => [
                'class' => IpBehavior::className(),
            ],
            'private_message_dialog_behavior' => [
                'class' => PrivateMessageDialogBehavior::className(),
            ],
        ];
    }

    public function sendMessage($from_user_id, $to_user_id, $message)
    {
        /* @var $private_message PrivateMessageEntity */
        $private_message = Yii::createObject(PrivateMessageEntity::className());
        $private_message_id = $private_message->getPrivateMessageId($from_user_id, $to_user_id);

        $model = clone $this;
        $data = [
            'private_message_id' => $private_message_id,
            'content'            => $message,
            'status'             => self::STATUS_UNREAD,
            'status'             => self::STATUS_UNREAD,
        ];

        if ($model->load($data, '') && $model->save()) {
            return $model->id;
        } else {
            Yii::error($this->getErrors(), __FUNCTION__);

            return false;
        }
    }

}