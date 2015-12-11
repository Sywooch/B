<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/23
 * Time: 10:04
 */

namespace common\entities;

use common\behaviors\OperatorBehavior;
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

    const ROLE_SENDER = 'sender';
    const ROLE_RECEIVER = 'receiver';

    public function behaviors()
    {
        return [
            'operator'                        => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'sender',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
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
        $model = self::findOne($id);

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

    private static function checkUserRole(PrivateMessage $model, $user_id)
    {
        if ($user_id == $model->sender) {
            $role = self::ROLE_SENDER;
        } else {
            $role = self::ROLE_RECEIVER;
        }

        return $role;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'sender']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'receiver']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrivateMessageDialogs()
    {
        return $this->hasMany(PrivateMessageDialogEntity::className(), ['private_message_id' => 'id']);
    }
}
