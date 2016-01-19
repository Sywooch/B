<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/6
 * Time: 9:36
 */

namespace common\entities;

use common\behaviors\UserEventBehavior;
use common\models\UserEvent;

class UserEventEntity extends UserEvent
{
    const STATUS_ENABLE = 'enable';
    const STATUS_DISABLE = 'disable';

    const NEED_RECORD = 'yes';
    const NO_NEED_RECORD = 'no';

    const PUBLIC_YES = 'yes';
    const PUBLIC_NO = 'no';

    public function behaviors()
    {
        return [
            'user_event' => [
                'class' => UserEventBehavior::className(),
            ],
        ];
    }
}
