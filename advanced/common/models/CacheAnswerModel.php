<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use Yii;

class CacheAnswerModel extends BaseCacheModel
{
    public $id;
    public $question_id;
    public $content;
    public $count_useful;
    public $count_comment;
    public $create_at;
    public $create_by;
    public $modify_at;
    public $modify_by;
    public $is_anonymous;
    public $is_fold;
}