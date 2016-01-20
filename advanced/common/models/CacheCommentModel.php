<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use common\entities\CommentEntity;
use Yii;

class CacheCommentModel extends BaseCacheModel
{
    public $id;
    public $associate_type;
    public $associate_id;
    public $content;
    public $count_vote = 0;
    public $count_like = 0;
    public $count_hate = 0;
    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;
    public $is_anonymous = 'no';
    public $status = CommentEntity::STATUS_ENABLE;
    public $ip;
    public $vote_status = false;
}
