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
    public $type;
    public $count_useful = 0;
    public $count_comment = 0;
    public $create_at;
    public $create_by;
    public $modify_at;
    public $modify_by;
    public $is_anonymous = 'no';
    public $is_fold = 'no';
}