<?php

namespace common\services;

use Yii;

class NotificationService extends BaseService
{

    public static function questionModify()
    {
        Yii::trace('�����޸�֪ͨ', 'notification');
    }


    public static function questionDelete($user_id, $question_id)
    {
        Yii::trace('����ɾ��֪ͨ', 'notification');
    }


    public static function removeFollowQuestion($user_id)
    {
        Yii::trace('ȡ�������ע֪ͨ', 'notification');
    }

}
