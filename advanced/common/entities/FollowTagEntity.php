<?php
/**
 * 主动关注TAG表，当用户提问时添加TAG时，这些TAG将自动关注。
 * 这些TAG只是用户主动关注的TAG，并不表示用户擅长这些，擅长TAG查看 FollowTagPassiveEntity
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/19
 * Time: 18:38
 */

namespace common\entities;

use common\components\Counter;
use common\components\Error;
use common\models\FollowTag;
use Yii;

class FollowTagEntity extends FollowTag
{
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

        if ($result) {
            #user follow tag count
            Counter::followTag($user_id, count($tag_ids));

        } else {
            Yii::error(sprintf('Batch Add Follow Tag %s', $result), __FUNCTION__);
        }

        return $result;
    }

    public static function removeFollowTag(array $tag_ids, $user_id = null)
    {
        if (empty($user_id) || empty($tag_ids)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id,tag_ids']);
        }

        #delete
        $model = self::find()->where(
            [
                'follow_tag_id' => $tag_ids,
            ]
        )->filterWhere(['user_id' => $user_id])->all();

        foreach ($model as $follow_tag) {
            if ($follow_tag->delete()) {
                Counter::cancelFollowTag($follow_tag->user_id);
            }
        }

        return true;
    }

    public static function getUserFollowTagIds($user_id, $limit = 20)
    {
        $cache_key = [REDIS_KEY_FOLLOW_TAG_USER_ID, implode('_', [$user_id, $limit])];
        $cache_data = Yii::$app->redis->get($cache_key);
        if ($cache_data === false) {
            $cache_data = self::find()->select(['follow_tag_id'])->where(
                [
                    'user_id' => $user_id,
                ]
            )->orderBy('modify_at DESC')->limit($limit)->asArray()->all();

            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }
}