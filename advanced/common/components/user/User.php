<?php
/**
 * 用户模型，实例：Yii::$app->user
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

    const STEP_REGISTER = 'register';
    const STEP_LOGIN = 'login';

    public $user_step;

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

    /**
     * 判断用户当前是否处于注册、登陆环节
     * @return bool
     */
    public function getStep()
    {
        return $this->user_step;
    }

    /**
     * 设置用户当前处于哪个环节
     * @param $step
     * @return mixed
     */
    public function setStep($step)
    {
        return $this->user_step = $step;
    }
}
