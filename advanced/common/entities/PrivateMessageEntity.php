<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/23
 * Time: 10:04
 */

namespace common\entities;


use common\behaviors\TimestampBehavior;
use common\exceptions\NotFoundModelException;
use common\models\PrivateMessage;
use yii\base\ErrorException;
use Yii;
use yii\db\ActiveRecord;

class PrivateMessageEntity extends PrivateMessage
{

    const STATUS_REMOVE_YES = 'yes';
    const STATUS_REMOVE_NO = 'no';

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],
        ];
    }
    
    public function getPrivateMessageId($from_user_id, $to_user_id)
    {
        $private_message_id = self::find()->select('id')->where(
            'sender=:sender OR receiver=:receiver',
            [
                ':sender'   => $from_user_id,
                ':receiver' => $to_user_id,
            ]
        )->scalar();

        if (!$private_message_id) {
            $model = clone $this;
            $model->sender = $from_user_id;
            $model->receiver = $to_user_id;
            if ($model->save()) {
                $private_message_id = $model->id;
            } else {
                Yii::error($model->getErrors(), __FUNCTION__);
                $private_message_id = null;
            }
        }

        return $private_message_id;
    }

    public function deletePrivateMessage($id, $user_id)
    {
        $model = self::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundModelException('Private Message');
        }

        if ($user_id == $model->sender) {
            $model->sender_remove = self::STATUS_REMOVE_YES;
        } elseif ($user_id == $model->receiver) {
            $model->sender_remove = self::STATUS_REMOVE_YES;
        } else {
            throw new ErrorException('You catn\'t delete this private message!');
        }

        if ($model->save()) {
            return true;
        } else {
            Yii::error($model->getErrors(), __FUNCTION__);

            return false;
        }
    }

    public function updateLastActive($id, $active_by, $last_message, $active_at)
    {
        $model = self::findOne($id);
        if ($model) {
            $model->last_message = $last_message;
            $model->active_by = $active_by;
            $model->active_at = $active_at;
            if ($model->save()) {
                return true;
            } else {
                Yii::error($model->getErrors(), __FUNCTION__);

                return false;
            }
        } else {
            Yii::error(sprintf('model no exist, id: %d', $id), __FUNCTION__);
        }
    }
    
    public function getDialogUserId($id, $user_id)
    {
        $model = self::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundModelException('Private Message');
        }

        if ($user_id == $model->sender) {
            $dialog_user_id = $model->receiver;
        } else {
            $dialog_user_id = $model->sender;
        }

        return $dialog_user_id;
    }
}