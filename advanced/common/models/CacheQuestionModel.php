<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use Yii;

class CacheQuestionModel extends BaseCacheModel
{
    public $id;
    public $subject;
    public $alias;
    public $tags;
    public $content;
    public $count_views = 0;
    public $count_answer = 0;
    public $count_favorite = 0;
    public $count_follow = 0;
    public $count_like = 0;
    public $count_hate = 0;
    public $create_at;
    public $create_by;
    public $active_at;
    public $is_anonymous = 'no';
    public $is_lock = 'no';
    public $status;
}











