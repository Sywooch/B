<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/16
 * Time: 19:07
 */

namespace common\entities;

use common\components\Error;
use common\models\QuestionTag;

class QuestionTagEntity extends QuestionTag
{
    /**
     * add question tag
     * @param       $user_id
     * @param       $question_id
     * @param array $tag_ids
     * @return bool
     */
    public static function addQuestionTag($user_id, $question_id, array $tag_ids)
    {
        if (empty($user_id) || empty($question_id) || empty($tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,question_id,tag_ids']);
        }

        $data = [];
        $create_at = time();

        foreach ($tag_ids as $tag_id) {
            $data[] = [$question_id, $tag_id, $user_id, $create_at];
        }

        #batch add question tag
        $result = self::getDb()->createCommand()->batchInsert(
            QuestionTag::tableName(),
            ['question_id', 'tag_id', 'create_by', 'create_at'],
            $data
        )->execute();

        #add follow tag
        if ($result) {
            #add user follow tag
            FollowTagEntity::addFollowTag($user_id, $tag_ids);
            #tag use count
            TagEntity::updateTagCountUse($tag_ids);
        }

        return $result;
    }
}
