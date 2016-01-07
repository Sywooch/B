<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 1/5
 * Time: 19:36
 */

namespace common\exceptions;

use common\entities\UserEventEntity;
use yii\base\Exception;

class CreditRuleDoesNotDefineException extends Exception
{
    public function __construct($event_id, $code = 500)
    {
        $event_name = UserEventEntity::find()->select('event')->where(
            [
                'id' => $event_id,
            ]
        )->scalar();

        $message = sprintf('积分变动规则: %s 未定义！', $event_name);
        parent::__construct($message, $code);
    }
}
