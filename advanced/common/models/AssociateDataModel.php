<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-02-23
 * Time: 11:59
 */

namespace common\models;

use yii\base\Model;

class AssociateDataModel extends Model
{
    public $question_id;
    public $answer_id;
    public $comment_id;
    public $tag_id;
    public $user_id;
}
