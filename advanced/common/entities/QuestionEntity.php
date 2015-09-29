<?php

namespace common\entities;

use common\models\Question;
use Yii;

class QuestionEntity extends Question
{
    public function afterSave($insert, $changedAttributes)
    {
        if (parent::afterSave($insert, $changedAttributes)) {

        }

        return true;
    }
}
