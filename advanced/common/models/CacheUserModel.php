<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use common\helpers\ArrayHelper;
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
    public $count_favorite = 0;
    public $count_question = 0;
    public $count_answer = 0;
    public $count_follow_user = 0;
    public $count_fans = 0;
    public $count_useful = 0;
    public $count_common_edit = 0;
    public $count_follow_question = 0;
    public $count_follow_tag = 0;
    public $count_home_views = 0;
    public $count_notification = 0;

    public $credit = 0;//信用
    public $currency = 0;//货币
    public $user_role_id = 0;//用户角色类型ID，刺客、武夫、
    public $user_grade_id = '';//用户等级ID

    /**
     * 自定义属性，合并user user_profile数据
     * @param $data
     * @return CacheUserModel
     */
    public function filterAttributes($data)
    {
        if (isset($data['profile'])) {
            $profile = $data['profile'];
            unset($data['profile']);
            $data = ArrayHelper::merge($data, $profile);
        }

        return parent::filterAttributes($data);
    }
}
