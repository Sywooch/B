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
