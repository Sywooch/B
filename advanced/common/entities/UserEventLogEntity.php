<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/16
 * Time: 11:27
 */

namespace common\entities;

use common\models\UserEventLog;

class UserEventLogEntity extends UserEventLog
{
    const ASSOCIATE_TYPE_QUESTION = 'question';
    const ASSOCIATE_TYPE_ANSWER = 'answer';
    const ASSOCIATE_TYPE_ANSWER_COMMENT = 'answer_comment';
    const ASSOCIATE_TYPE_USER = 'user';
    const ASSOCIATE_TYPE_TAG = 'tag';
}
