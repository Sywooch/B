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
    public $create_at;
    public $create_by;
    public $tags;
}