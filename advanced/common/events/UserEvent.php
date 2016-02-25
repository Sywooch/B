<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/3
 * Time: 19:23
 */

namespace common\events;

use common\components\user\UserAssociationEvent;
use common\entities\FollowEntity;
use common\models\AssociateModel;
use common\models\NoticeDataModel;
use common\services\UserService;
use Yii;

class UserEvent extends BaseUserEvent
{
    public static function follow(FollowEntity $owner)
    {
        $user = UserService::getUserById($owner->associate_id);

        $notice_data = new NoticeDataModel();
        $notice_data->sender = $owner->user_id;
        $notice_data->receiver = $user->id;

        Yii::$app->user->trigger(
            'event_user_follow',
            new UserAssociationEvent(
                [
                    'id'             => $user->id,
                    'type'           => AssociateModel::TYPE_USER,
                    'associate_data' => [],
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    public static function cancelFollow(FollowEntity $owner)
    {
        $user = UserService::getUserById($owner->associate_id);

        $notice_data = new NoticeDataModel();

        Yii::$app->user->trigger(
            'event_user_cancel_follow',
            new UserAssociationEvent(
                [
                    'id'             => $user->id,
                    'type'           => AssociateModel::TYPE_USER,
                    'associate_data' => [],
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }
}
