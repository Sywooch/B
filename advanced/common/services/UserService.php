<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 10:23
 */

namespace common\services;


use common\entities\UserEntity;
use common\entities\UserProfileEntity;
use Yii;

class UserService extends BaseService
{
    public $user, $user_profile;

    public function __construct(UserEntity $user, UserProfileEntity $user_profile)
    {
        $this->user = $user;
        $this->user_profile = $user_profile;
    }

    public function increaseNotificationCount(array $user_ids)
    {
        $this->user_profile->updateAllCounters(['count_notification' => 1], ['user_id' => $user_ids]);
    }

    public function clearNotificationCount(array $user_ids)
    {
        $this->user_profile->updateAll(['count_notification' => 0], ['user_id' => $user_ids]);
    }

    public function deleteAllNotification(array $user_ids)
    {
        $this->user_profile->deleteAll(['user_id' => $user_ids]);
    }

}