<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 18:38
 */

namespace common\entities;


use common\components\Counter;
use common\models\FollowTag;

class FollowTagEntity extends FollowTag
{
    public function addFollowTag($user_id, array $tag_ids)
    {
        if (empty($user_id) || empty($tag_ids)) {
            throw new ParamsInvalidException(['user_id', 'tag_ids']);
        }

        $data = [];
        $create_at = time();

        foreach ($tag_ids as $tag_id) {
            $data[] = [$user_id, $tag_id, $create_at];
        }

        #batch add
        $sql = self::getDb()->createCommand()->batchInsert(
            self::tableName(),
            ['user_id', 'follow_tag_id', 'create_at'],
            $data
        )->getSql();

        $result = self::getDb()->createCommand(
            sprintf(
                '%s ON DUPLICATE KEY UPDATE modify_at="%d", count_follow=count_follow+1',
                $sql,
                time()
            )
        )->execute();

        if ($result) {
            Counter::build()->set(UserProfileEntity::tableName(), $user_id, 'user_id')->value(
                'count_follow_tag',
                count($tag_ids)
            )->execute();

        } else {
            Yii::error(sprintf('Batch Add Follow Tag %s', $result), __FUNCTION__);
        }

        return $result;
    }

    public function removeFollowTag(array $tag_ids, $user_id = null)
    {
        if (empty($user_id) || empty($tag_ids)) {
            throw new ParamsInvalidException(['user_id', 'tag_ids']);
        }

        #delete
        $model = self::find(
            [
                'follow_tag_id' => $tag_ids,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        foreach ($model as $follow_tag) {
            if ($follow_tag->delete()) {
                Counter::build()->set(
                    UserProfileEntity::tableName(),
                    $follow_tag->user_id,
                    'user_id'
                )->value(
                    'count_follow_question',
                    -1
                )->execute();
            }
        }

        return true;
    }
}