<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use Yii;

class CacheUserModel extends BaseCacheModel
{
    public $id;
    public $name;
    public $username;
    public $avatar;
    public $email;
    public $sex;
    public $title;
    public $bio;
    public $last_login_at;
    public $count_favorite;
    public $count_question;
    public $count_answer;
    public $count_follow;
    public $count_be_follow;
    public $count_usefull;
    public $count_common_edit;
    public $count_follow_question;
    public $count_follow_tag;
    public $count_home_views;
    public $count_notification;

    public function filterAttributes($data)
    {
        if (isset($data['profile'])) {
            $profile = $data['profile'];
            unset($data['profile']);
            $data = array_merge($data, $profile);
        }

        return parent::filterAttributes($data);
    }
}












