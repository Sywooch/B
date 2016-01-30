<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-01-28
 * Time: 10:20
 */

namespace common\models;

use common\config\RedisKey;
use common\entities\FollowEntity;
use common\exceptions\NotFoundModelException;
use common\services\FollowService;
use common\services\UserService;
use Yii;

class FollowUserModel extends FollowModel
{
    public $model;

    public function __construct($associate_id, $user_id)
    {
        parent::__construct($associate_id, $user_id);

        //修改点
        $this->associate_type = AssociateModel::TYPE_USER;
        $this->cache_key = [RedisKey::REDIS_KEY_USER_FANS_LIST, $this->associate_id];
        $this->model = UserService::getUserById($this->associate_id);

        if (!$this->model) {
            throw new NotFoundModelException($this->associate_type, $this->associate_id);
        }
    }

    public function checkWhetherUseCache()
    {
        //修改点
        return ($this->model->count_follow_user < FollowService::MAX_FOLLOW_USER_COUNT_BY_USING_CACHE);
    }
    
    public function checkWhetherOverMaxFollowNumber()
    {
        //修改点
        return ($this->model->count_follow_user > FollowEntity::MAX_FOLLOW_USER_NUMBER);
    }
}
