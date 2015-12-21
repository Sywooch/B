<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/3
 * Time: 20:07
 */

namespace common\components\user;

use common\behaviors\UserEventBehavior;
use common\helpers\ArrayHelper;

class User extends \yii\web\User
{
    const EVENT_AFTER_CREATE_QUESTION = 'event_after_create_question';
    const EVENT_AFTER_COMMON_EDIT_QUESTION = 'event_after_common_edit_question';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'user_event_behavior' => [
                    'class' => UserEventBehavior::className(),
                ],
            ]
        );
    }
}
