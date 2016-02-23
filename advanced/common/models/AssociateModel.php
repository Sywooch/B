<?php
/**
 * Created by PhpStorm.
 * User: Keen
 * Date: 2016/1/16
 * Time: 17:48
 */

namespace common\models;

use yii\base\Model;

class AssociateModel extends Model
{
    const TYPE_QUESTION = 'question';
    const TYPE_ANSWER = 'answer';
    const TYPE_BLOG = 'blog';
    const TYPE_COMMENT = 'comment';
    const TYPE_ANSWER_COMMENT = 'answer_comment';
    const TYPE_USER = 'user';
    const TYPE_TAG = 'tag';
    const TYPE_TAG_PASSIVE = 'tag_passive';

    /**
     * 投票关联类型中，对问题、回答、回答评论进行投票，都属于问题
     * @param $associate_type
     * @return string
     */
    public static function dealWithVoteAssociateType($associate_type)
    {
        if (in_array($associate_type, [self::TYPE_QUESTION, self::TYPE_ANSWER, self::TYPE_ANSWER_COMMENT])) {
            $type = self::TYPE_QUESTION;
        } else {
            //todo
            $type = $associate_type;
        }

        return $type;
    }
}
