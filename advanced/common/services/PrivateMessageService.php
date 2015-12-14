<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 15:09
 */

namespace common\services;

use common\entities\PrivateMessageEntity;
use common\exceptions\NotFoundModelException;

class PrivateMessageService extends BaseService
{

    public static function getPrivateMessageId($from_user_id, $to_user_id)
    {
        $private_message_id = PrivateMessageEntity::find()->select('id')->where(
            'sender=:sender OR receiver=:receiver',
            [
                ':sender'   => $from_user_id,
                ':receiver' => $to_user_id,
            ]
        )->scalar();

        if (!$private_message_id) {
            $model = new PrivateMessageEntity();
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

    public static function deletePrivateMessage($id, $user_id)
    {
        /* @var $model PrivateMessageEntity */
        $model = PrivateMessageEntity::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundModelException('Private Message', $id);
        }

        if ($user_id == $model->sender) {
            $model->sender_remove = self::STATUS_REMOVE_YES;
        } elseif ($user_id == $model->receiver) {
            $model->sender_remove = self::STATUS_REMOVE_YES;
        } else {
            throw new ErrorException('You catn\'t delete this private message!');
        }

        if ($model->save()) {
            if ($model->sender_remove == self::STATUS_REMOVE_YES && $model->sender_remove == self::STATUS_REMOVE_YES) {
                $model->delete();
            }

            return true;
        } else {
            Yii::error($model->getErrors(), __FUNCTION__);

            return false;
        }
    }

    public static function updateLastActive($id, $active_by, $last_message, $updated_at)
    {
        /* @var $model PrivateMessageEntity */
        $model = PrivateMessageEntity::findOne($id);

        if ($model) {
            $model->last_message = $last_message;
            $model->updated_by = $active_by;
            $model->updated_at = $updated_at;

            #update read status
            $currentUserRole = self::checkUserRole($model, $active_by);
            if ($currentUserRole == self::ROLE_SENDER) {
                $model->sender_read_at = time();
            } else {
                $model->receiver_read_at = time();
            }

            if ($model->save()) {
                return true;
            } else {
                Yii::error($model->getErrors(), __FUNCTION__);

                return false;
            }
        } else {
            Yii::error(sprintf('model no exist, id: %d', $id), __FUNCTION__);

            return false;
        }
    }

    /**
     * find dialog object's user id
     * @param $id
     * @param $user_id
     * @return int
     * @throws NotFoundModelException
     */
    public static function getDialogUserId($id, $user_id)
    {
        $model = PrivateMessageEntity::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundModelException('Private Message', $id);
        }

        if ($user_id == $model->sender) {
            $dialog_user_id = $model->receiver;
        } else {
            $dialog_user_id = $model->sender;
        }

        return $dialog_user_id;
    }

    private static function checkUserRole(PrivateMessage $model, $user_id)
    {
        if ($user_id == $model->sender) {
            $role = self::ROLE_SENDER;
        } else {
            $role = self::ROLE_RECEIVER;
        }

        return $role;
    }

    public static function sendMessage($from_user_id, $to_user_id, $message)
    {
        $private_message_id = PrivateMessageService::getPrivateMessageId($from_user_id, $to_user_id);

        $model = new PrivateMessageEntity();
        $data = [
            'private_message_id' => $private_message_id,
            'content'            => $message,
            'status'             => self::STATUS_UNREAD,
        ];

        if ($model->load($data, '') && $model->save()) {
            return $model->id;
        } else {
            Yii::error($model->getErrors(), __FUNCTION__);

            return false;
        }
    }
}