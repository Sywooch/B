<?php

namespace common\services;

use Yii;

class NotificationService extends BaseService
{

    public static function questionModify()
    {
        Yii::trace('问题修改通知', 'notification');
    }


    public static function questionDelete($user_id, $question_id)
    {
        Yii::trace('问题删除通知', 'notification');
    }


    public static function removeFollowQuestion($user_id)
    {
        Yii::trace('取消问题关注通知', 'notification');
    }

}
