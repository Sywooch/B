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
    public $count_views;
    public $count_answer;
    public $count_favorite;
    public $count_follow;
    public $count_like;
    public $count_hate;
    public $create_at;
    public $create_by;
    public $active_at;
    public $is_anonymous;
    public $is_lock;
    public $status;
}











