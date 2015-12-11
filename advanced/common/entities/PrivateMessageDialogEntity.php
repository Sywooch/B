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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                ],
            ],
            'timestamp'                       => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrivateMessage()
    {
        return $this->hasOne(PrivateMessageEntity::className(), ['id' => 'private_message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'created_by']);
    }
}
