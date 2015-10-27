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
use common\models\FollowTagPassive;

class FollowTagPassiveEntity extends FollowTagPassive
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

        if (!$result) {
            Yii::error(sprintf('Batch Add Passive Follow Tag %s', $result), __FUNCTION__);
        }

        return $result;
    }
}