<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 11:39
 */

namespace common\services;

use common\config\RedisKey;
use common\entities\TagEntity;
use common\entities\TagRelationEntity;
use common\exceptions\ModelSaveErrorException;
use common\helpers\TimeHelper;
use common\models\CacheTagModel;
use Yii;

class TagService extends BaseService
{
    /**
     * batch add tags
     * @param array $tags ['tag_name']
     * @return array ['tag_name'=> 'tag_id']
     */
    public static function batchAddTags(array $tags)
    {
        $data = [];
        foreach ($tags as $tag_name) {
            /* @var $model TagEntity */
            $model = TagEntity::findOne(['name' => $tag_name]);

            if (!$model) {
                $model = new TagEntity;
                $model->name = $tag_name;
                if (!$model->save()) {
                    throw new ModelSaveErrorException($model);
                }
            } elseif ($model->status == TagEntity::STATUS_DISABLE) {
                #如果存在，但被禁用,则跳过
                continue;
            }

            $data[$tag_name] = $model->id;
        }

        return $data;
    }

    /**
     * get tag ids by tag name
     * @param array $tags
     * @return mixed
     */
    public static function batchGetTagIds(array $tags)
    {
        $model = TagEntity::find()->where(['name' => $tags, 'status' => TagEntity::STATUS_ENABLE])->asArray()->all();

        return $model;
    }

    /**
     * TODO
     * @param $question_id
     */
    public static function getTagsByQuestionId($question_id)
    {

    }

    public static function getTagNameById($tag_id)
    {
        if (is_array($tag_id)) {
            $multiple = true;
        } else {
            $multiple = false;
            $tag_id = [$tag_id];
        }

        $data = self::getTagListByTagIds($tag_id);

        $tag_name_id = [];
        foreach ($data as $item) {
            $tag_name_id[$item['name']] = $item['id'];
        }

        if ($multiple) {
            $result = $tag_name_id;
        } else {
            $result = array_shift($tag_name_id);
        }

        return $result;
    }

    public static function getTagIdByName($tag_name)
    {
        if (is_array($tag_name)) {
            $multiple = true;
        } else {
            $multiple = false;
            $tag_name = [$tag_name];
        }


        $tag_name = array_values(array_unique(array_filter($tag_name)));
        //print_r($tag_name);exit;

        $data = self::getTagIdByNameUseCache($tag_name);
        if ($data) {
            $combine_data = array_combine($tag_name, $data);
        } else {
            $combine_data = $tag_name;
        }

        if ($multiple) {
            $result = $combine_data;
        } else {
            $result = array_shift($combine_data);
        }

        return $result;
    }

    private static function getTagIdByNameUseCache($tag_name)
    {
        $cache_hit_data = Yii::$app->redis->mget([RedisKey::REDIS_KEY_TAG_NAME_ID, $tag_name]);
        $cache_miss_key = Yii::$app->redis->getMissKey($tag_name, $cache_hit_data);

        if (count($cache_miss_key)) {

            $model = TagEntity::find()->select(['id', 'name'])->where(
                [
                    'name' => $cache_miss_key,
                ]
            )->all();

            #$cache_miss_data 为数组，格式key为索引ID，value为保存到redis中的数据
            $cache_miss_data = [];
            foreach ($model as $key => $item) {
                #load useful attributes
                $cache_miss_data[$item['name']] = $item['id'];
            }


            if ($cache_miss_data) {
                #cache user miss databases data
                Yii::$app->redis->mset([RedisKey::REDIS_KEY_TAG_NAME_ID, $cache_miss_data]);

                #padding miss data
                $cache_hit_data = Yii::$app->redis->paddingMissData(
                    $cache_hit_data,
                    $cache_miss_key,
                    $cache_miss_data
                );
            }
        }

        return $cache_hit_data;
    }

    public static function getRecommendTag($word)
    {
        $result = Yii::$app->cws->getTops($word, 10);

        $tags = [];

        foreach ($result as $item) {
            $tags[] = '';
        }

        return $tags;
    }

    public static function ensureTagHasCached($tag_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_TAG, $tag_id];
        if (Yii::$app->redis->hLen($cache_key) == 0) {
            return self::getTagByTagId($tag_id);
        }

        return true;
    }

    public static function deleteTagCache($tag_id)
    {
        $cache_key = [RedisKey::REDIS_KEY_TAG, $tag_id];

        return Yii::$app->redis->delete($cache_key);
    }

    /**
     * @param $tag_id
     * @return CacheTagModel
     * @throws NotFoundModelException
     */
    public static function getTagByTagId($tag_id)
    {
        $data = self::getTagListByTagIds([$tag_id]);
        if ($data) {
            return array_shift($data);
        } else {
            throw new NotFoundModelException('tag', $tag_id);
        }
    }

    /**
     * @param array $tag_ids
     * @return array|CacheTagModel
     */
    public static function getTagListByTagIds(array $tag_ids)
    {
        //去重
        if (empty($tag_ids)) {
            return [];
        } else {
            $tag_ids = array_unique($tag_ids);
        }

        $result = $cache_miss_key = $cache_data = [];
        $cache_question_model = new CacheTagModel();

        foreach ($tag_ids as $tag_id) {
            $cache_key = [RedisKey::REDIS_KEY_TAG, $tag_id];
            $cache_data = Yii::$app->redis->hGetAll($cache_key);
            if (empty($cache_data)) {
                $cache_miss_key[] = $tag_id;
                $result[$tag_id] = null;
            } else {
                $result[$tag_id] = $cache_question_model->build($cache_data);
            }
        }

        if ($cache_miss_key) {
            $cache_data = TagEntity::find()->where(
                [
                    'id' => $cache_miss_key,
                ]
            )->asArray()->all();

            foreach ($cache_data as $item) {
                #filter attributes
                $data = $cache_question_model->filter($item);

                $tag_id = $data['id'];
                $result[$tag_id] = $cache_question_model->build($data);
                $cache_key = [RedisKey::REDIS_KEY_TAG, $tag_id];
                Yii::$app->redis->hMset($cache_key, $data);
            }
        }

        return $result;
    }

    public static function getHotTag($limit = 20, $period = 100)
    {
        $tag_ids = self::getHotTagIds($limit, $period);
        if ($tag_ids) {
            $tags = self::getTagListByTagIds($tag_ids);
        } else {
            $tags = [];
        }

        return $tags;
    }

    /**
     * @param     $tag_id
     * @param int $limit
     * @return array ['type' => ['tag_id' => ['name' => '','count_relation']]]
     */
    public static function getRelateTag($tag_id, $limit = 100)
    {
        $cache_key = [RedisKey::REDIS_KEY_RELATE_TAG, implode(':', [$tag_id, $limit])];
        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $tag_relate_list = TagRelationEntity::find()->where(
                [
                    'or',
                    '`tag_id_1`=:tag_id',
                    '`tag_id_2`=:tag_id',
                ],
                [':tag_id' => $tag_id]
            )->andWhere(
                [
                    'status' => TagRelationEntity::STATUS_ENABLE,
                ]
            )->orderBy('count_relation DESC')->limit($limit)->asArray()->all();

            /*$relate_tag_ids = [
                'brother' => [
                    'tag_id' => [
                        'name' => '',
                        'count_relation',
                    ],
                ],
            ];*/

            $relate_tag = $relate_tag_ids = [];
            foreach ($tag_relate_list as $item) {
                if ($item['tag_id_1'] == $tag_id) {
                    $relate_tag[$item['type']][$item['tag_id_2']] = [
                        'id'             => $item['tag_id_2'],
                        'name'           => '',
                        'count_relation' => $item['count_relation'],
                    ];
                    $relate_tag_ids[] = $item['tag_id_2'];
                } else {
                    $relate_tag[$item['type']][$item['tag_id_1']] = [
                        'id'             => $item['tag_id_1'],
                        'name'           => '',
                        'count_relation' => $item['count_relation'],
                    ];
                    $relate_tag_ids[] = $item['tag_id_1'];
                }
            }

            $tags = self::getTagListByTagIds($relate_tag_ids);

            foreach ($relate_tag as $type => $item) {
                foreach ($item as $tag_id => $tag) {
                    $relate_tag[$type][$tag_id]['name'] = $tags[$tag_id]['name'];
                }
            }

            $cache_data = $relate_tag;
            Yii::$app->redis->set($cache_key, $cache_data);
        }

        return $cache_data;
    }

    private static function getHotTagIds($limit = 20, $period = 30)
    {
        $cache_key = [RedisKey::REDIS_KEY_TAG_LIST, implode('_', ['HOT', $limit, $period])];

        if (0 === Yii::$app->redis->zCard($cache_key)) {
            $result = TagEntity::find()->where(
                'count_use>=:count_use AND count_follow>=:count_follow AND updated_at>=:updated_at',
                [
                    ':count_use'    => 1,
                    ':count_follow' => 1,
                    ':updated_at'   => TimeHelper::getBeforeTime($period),
                ]
            )->orderBy('count_follow DESC')->limit($limit)->asArray()->all();

            $params = [
                $cache_key,
            ];

            foreach ($result as $item) {
                $params[] = $item['count_use'] + $item['count_follow'];
                $params[] = $item['id'];
            }

            call_user_func_array([Yii::$app->redis, 'zAdd'], $params);
        }

        $page_no = 1;
        $page_size = $limit;
        $start = ($page_no - 1) * $page_size;
        $end = $page_size * $page_no - 1;

        $result = Yii::$app->redis->zRevRange($cache_key, $start, $end);

        return $result;
    }

    public static function updateTagCountUse(array $tag_ids)
    {
        return TagEntity::updateAllCounters(['count_use' => 1], ['id' => $tag_ids]);
    }

    /**
     * 更新问题缓存
     * @param $tag_id
     * @param $data
     * @return bool
     */
    public static function updateTagCache($tag_id, $data)
    {
        $cache_key = [RedisKey::REDIS_KEY_TAG, $tag_id];
        if ($tag_id && $data && Yii::$app->redis->hLen($cache_key)) {
            return Yii::$app->redis->hMset($cache_key, $data);
        }

        return false;
    }
}