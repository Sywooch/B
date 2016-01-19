<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:13
 */

namespace common\models;

use Yii;

class CacheAnswerCommentModel extends BaseCacheModel
{
    public $id;
    public $answer_id;
    public $content;
    public $type;
    public $count_vote = 0;
    public $count_like = 0;
    public $count_hate = 0;
    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;
    public $is_anonymous = 'no';
    public $is_fold = 'no';
    public $vote_status = false;
}
