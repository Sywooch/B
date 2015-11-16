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


        $tag_id = array_filter($tag_id);
        $data = self::getTagNameByIdUseCache($tag_id);

        if ($multiple) {
            $result = $data;
        } else {
            $result = array_shift($data);
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


        $tag_name = array_filter($tag_name);
        $data = self::getTagIdByNameUseCache($tag_name);

        if ($multiple) {
            $result = $data;
        } else {
            $result = array_shift($data);
        }

        return $result;
    }

    private static function getTagNameByIdUseCache($tag_id)
    {
        $cache_hit_data = Yii::$app->redis->mget([REDIS_KEY_TAG_ID_NAME, $tag_id]);
        $cache_miss_key = Yii::$app->redis->getMissKey($tag_id, $cache_hit_data);

        if (count($cache_miss_key)) {
            $sql = sprintf(
                "SELECT t.id, t.name
                FROM `%s` t
                WHERE t.id
                IN(%s)",
                TagEntity::tableName(),
                "'" . implode("','", $cache_miss_key) . "'"
            );

            $model = self::getDb()->createCommand($sql)->queryAll();

            #$cache_miss_data 为数组，格式key为索引ID，value为保存到redis中的数据
            $cache_miss_data = [];
            $tag_name_id_data = [];
            foreach ($model as $key => $item) {
                #load useful attributes
                $cache_miss_data[$item['id']] = $item['name'];
                $tag_name_id_data[$item['name']] = $item['id'];
            }

            if ($cache_miss_data) {
                #cache user miss databases data
                Yii::$app->redis->mset([REDIS_KEY_TAG_ID_NAME, $cache_miss_data]);

                #cache username id relation data
                Yii::$app->redis->mset([REDIS_KEY_TAG_NAME_ID, $tag_name_id_data]);

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
            $tag_id_name_data = [];
            foreach ($model as $key => $item) {
                #load useful attributes
                $cache_miss_data[$item['name']] = $item['id'];
                $tag_id_name_data[$item['id']] = $item['name'];
            }

            if ($cache_miss_data) {
                #cache user miss databases data
                Yii::$app->redis->mset([REDIS_KEY_TAG_NAME_ID, $cache_miss_data]);

                #cache username id relation data
                Yii::$app->redis->mset([REDIS_KEY_TAG_ID_NAME, $tag_id_name_data]);

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
        if (Yii::$app->redis->hLen($cache_key) === 0) {
            self::getTagByTagId($tag_id);
        }

        return true;
    }


    public static function getTagByTagId($tag_id)
    {
        $data = self::getTagListByTagIds([$tag_id]);

        return $data ? array_shift($data) : null;
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
    
    public static function getHotTags($limit = 20)
    {
        $result = self::find()->where(
            'count_follow>=:count_follow',
            [':count_follow' => 10]

        )->orderBy('active_at DESC')->limit($limit)->asArray()->all();

        return $result;
    }

    public static function updateTagCountUse(array $tag_ids)
    {
        return self::updateAllCounters(['count_use' => 1], ['id' => $tag_ids]);
    }
}
