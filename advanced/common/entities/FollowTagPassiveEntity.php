<?php
/**
 * 被动TAG关注表，当用户回答某个问题时，则自动被动关注这个问题的TAG
 * 这些TAG将被计算为用户擅长的TAG
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 18:38
 */

namespace common\entities;


use common\components\Error;
use common\helpers\ArrayHelper;
use common\models\FollowTagPassive;
use Yii;

class FollowTagPassiveEntity extends FollowTagPassive
{
    const MAX_NUMBER_RECOMMEND_USER = 10;
    const RECENT_PERIOD_OF_TIME = 15; #距最后tag活跃的天数

    public static function addFollowTag($user_id, array $tag_ids)
    {
        if (empty($user_id) || empty($tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,tag_ids']);
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

    /**
     * @param array $tag_ids
     * @param int   $number
     * @return array ['tag_id'=>['user_id' => 'count_question', 'user_id' => 'count_question'], 'tag_id' => []]
     */
    public static function getRecommendUserIdsByTagIds(array $tag_ids, $number = self::MAX_NUMBER_RECOMMEND_USER)
    {
        $relation = [];
        foreach ($tag_ids as $tag_id) {
            $relation[$tag_id] = self::getTagAndUserRelation($tag_id);
        }
        //print_r($relation);exit;

        $total_user_number = 0;
        foreach ($relation as $item) {
            $total_user_number += count($item);
        }


        $result = [];
        foreach ($relation as $tag_id => $item) {
            $item_length = count($item);
            $split_number = round($item_length / $total_user_number * $number);

            if ($split_number < $item_length) {
                if ($split_number > 0) {
                    $result[$tag_id] = array_slice($item, 0, $split_number, true);
                }
            } else {
                $result[$tag_id] = $item;
            }
        }

        return $result;
    }

    public static function getTagAndUserRelation($tag_id)
    {
        if (!Yii::$app->redis->zCard([REDIS_KEY_TAG_USER_RELATION, $tag_id])) {
            $sql = sprintf(
                "SELECT
              tp.`follow_tag_id`,
              tp.`user_id`,
              SUM(tp.`count_follow`) AS `score`
            FROM
              %s tp
            WHERE tp.`follow_tag_id`=:follow_tag_id
            AND modify_at>=:modify_at
            GROUP BY tp.`user_id`
            ORDER BY `score` DESC
            LIMIT 10;",
                self::tableName()

            );

            $command = self::getDb()->createCommand(
                $sql,
                [
                    ':follow_tag_id' => $tag_id,
                    ':modify_at'     => time() - (self::RECENT_PERIOD_OF_TIME * 86400),
                ]
            );

            //exit($command->getRawSql());
            $data = $command->queryAll();

            $relation = [];
            foreach ($data as $item) {
                $relation[$item['user_id']] = $item['score'];
            }

            //print_r($relation);exit;

            if ($relation) {
                self::addTagAndUserRelation($tag_id, $relation);
            }
        }

        $result = Yii::$app->redis->zRevRange(
            [REDIS_KEY_TAG_USER_RELATION, $tag_id],
            0,
            -1,
            true
        );

        //print_r($result);
        //exit;

        return $result;
    }

    private static function addTagAndUserRelation($tag_id, $relation)
    {
        $params = [
            [REDIS_KEY_TAG_USER_RELATION, $tag_id],
        ];

        foreach ($relation as $user_id => $score) {
            $params[] = $score;
            $params[] = $user_id;
        }

        //print_r($params);exit;

        $result = call_user_func_array([Yii::$app->redis, 'zAdd'], $params);

        return $result;
    }

    public static function cleanTagAndUserRelation($tag_id)
    {

    }
}