<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/11
 * Time: 11:29
 */

namespace common\entities;

use common\models\AnswerUsefulLog;

class AnswerUsefulLogEntity extends AnswerUsefulLog
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(AnswerEntity::className(), ['id' => 'answer_id']);
    }
}
