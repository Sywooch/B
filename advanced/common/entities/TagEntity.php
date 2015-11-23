<?php

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TagBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\exceptions\ParamsInvalidException;
use common\models\CacheTagModel;
use common\models\FollowTag;
use common\models\QuestionTag;
use common\models\TagQuery;
use Yii;
use common\models\Tag;
use yii\db\ActiveRecord;
use yii\db\QueryBuilder;
use yii\helpers\ArrayHelper;

class TagEntity extends Tag
{

    const STATUS_ENABLE = 'enable';
    const STATUS_DISABLE = 'disable';


    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                    ActiveRecord::EVENT_BEFORE_UPDATE   => 'modify_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'modify_at',
                ],
            ],
            'tag'       => [
                'class' => TagBehavior::className(),
            ],
        ];
    }

    /**
     * batch add tags
     * @param array $tags ['tag_name']
     * @return array ['tag_name'=> 'tag_id']
     */
    public static function batchAddTags(array $tags)
    {
        $data = [];
        foreach ($tags as $tag_name) {
            $model = self::findOne(['name' => $tag_name]);

            if (!$model) {
                $model = new self;
                $model->name = $tag_name;
                if (!$model->save()) {
                    Yii::error($model->getErrors(), __FUNCTION__);
                }
            } elseif ($model->status == self::STATUS_DISABLE) {
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
    public function batchGetTagIds(array $tags)
    {
        $model = self::find()->where(['name' => $tags, 'status' => SELF::STATUS_ENABLE])->asArray()->all();

        return $model;
    }

    public static function getTagsByQuestionId($question_id)
    {

    }


    /**
     * remove question tag
     * @param       $user_id
     * @param       $question_id
     * @param array $tag_ids
     * @return int
     */
    public function removeQuestionTag($user_id, $question_id, array $tag_ids)
    {
        $result = self::getDb()->createCommand()->delete(
            QuestionTag::tableName(),
            [
                'create_by'   => $user_id,
                'question_id' => $question_id,
                'tag_id'      => $tag_ids,
            ]
        )->execute();

        return $result;
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

        $combine_data = array_combine($tag_name, $data);
        if ($multiple) {
            $result = $combine_data;
        } else {
            $result = array_shift($combine_data);
        }

        return $result;
    }

    private static function getTagIdByNameUseCache($tag_name)
    {
        $cache_hit_data = Yii::$app->redis->mget([REDIS_KEY_TAG_NAME_ID, $tag_name]);
        $cache_miss_key = Yii::$app->redis->getMissKey($tag_name, $cache_hit_data);

        if (count($cache_miss_key)) {
            $sql = sprintf(
                "SELECT t.id, t.name
                FROM `%s` t
                WHERE t.name
                IN(%s)",
                TagEntity::tableName(),
                "'" . implode("','", $cache_miss_key) . "'"
            );

            $model = self::getDb()->createCommand($sql)->queryAll();

            #$cache_miss_data 为数组，格式key为索引ID，value为保存到redis中的数据
            $cache_miss_data = [];
            foreach ($model as $key => $item) {
                #load useful attributes
                $cache_miss_data[$item['name']] = $item['id'];
            }


            if ($cache_miss_data) {
                #cache user miss databases data
                Yii::$app->redis->mset([REDIS_KEY_TAG_NAME_ID, $cache_miss_data]);

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
    
    public function getRecommendTag($word)
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
        $cache_key = [REDIS_KEY_TAG, $tag_id];
        if (Yii::$app->redis->hLen($cache_key) == 0) {
           return self::getTagByTagId($tag_id);
        }

        return true;
    }


    public static function getTagByTagId($tag_id)
    {
        $data = self::getTagListByTagIds([$tag_id]);

        return $data ? array_shift($data) : false;
    }

    public static function getTagListByTagIds(array $tag_ids)
    {
        $result = $cache_miss_key = $cache_data = [];
        foreach ($tag_ids as $tag_id) {
            $cache_key = [REDIS_KEY_TAG, $tag_id];
            $cache_data = Yii::$app->redis->hGetAll($cache_key);
            if (empty($cache_data)) {
                $cache_miss_key[] = $tag_id;
                $result[$tag_id] = null;
            } else {
                $result[$tag_id] = $cache_data;
            }
        }

        if ($cache_miss_key) {
            $cache_data = self::find()->where(
                [
                    'id' => $cache_miss_key,
                ]
            )->asArray()->all();

            $cache_question_model = new CacheTagModel();
            foreach ($cache_data as $item) {
                #filter attributes
                $item = $cache_question_model->filterAttributes($item);
                $tag_id = $item['id'];
                $result[$tag_id] = $item;
                $cache_key = [REDIS_KEY_QUESTION, $tag_id];
                Yii::$app->redis->hMset($cache_key, $item);
            }
        }

        return $result;
    }

    public static function getHotTag()
    {
        $tag_ids = self::getHotTagIds();
        if ($tag_ids) {
            $tags = self::getTagListByTagIds($tag_ids);
        } else {
            $tags = [];
        }

        return $tags;
    }

    public static function getRelateTag($tag_id, $limit = 20)
    {
        $tag_relate_list = TagRelationEntity::find()->where(
            '`tag_id_1`=:tag_id',
            [':tag_id' => $tag_id]
        )->orderBy('count_relation DESC')->limit($limit)->asArray()->all();


        $tag_ids = ArrayHelper::getColumn($tag_relate_list, 'tag_id_2');
        $tags = TagEntity::getTagListByTagIds($tag_ids);

        $result = [];
        foreach ($tag_relate_list as $key => $tag) {
            if (!empty($tags[$tag_relate_list[$key]['tag_id_2']]['name'])) {
                $result[] = [
                    'id'             => $tag['tag_id_2'],
                    'name'           => $tags[$tag_relate_list[$key]['tag_id_2']]['name'],
                    'type'           => $tag['type'],
                    'count_relation' => $tag['count_relation'],
                ];
            }
        }

        return $result;
    }
    
    private static function getHotTagIds($limit = 20)
    {
        $cache_key = [REDIS_KEY_TAG_LIST, 'HOT' . $limit];

        if (0 === Yii::$app->redis->zCard($cache_key)) {
            $result = self::find()->where(
                'count_use>=:count_use AND count_follow>=:count_follow',
                [
                    ':count_use'    => 1,
                    ':count_follow' => 1,
                ]

            )->orderBy('active_at DESC')->limit($limit)->asArray()->all();


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
        return self::updateAllCounters(['count_use' => 1], ['id' => $tag_ids]);
    }
}
