<?php

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\TagBehavior;
use common\behaviors\TimestampBehavior;
use common\exceptions\ParamsInvalidException;
use common\models\FollowTag;
use common\models\TagQuery;
use Yii;
use common\models\Tag;
use yii\db\ActiveRecord;
use yii\db\QueryBuilder;
use yii\helpers\ArrayHelper;

class TagEntity extends Tag
{

    public function behaviors()
    {
        return [
            'operator' => [
                'class' => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'modify_by',
                ],
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'modify_at',
                ],
            ],
            'tag' => [
                'class' => TagBehavior::className()
            ]
        ];
    }

    /**
     * batch add tags
     * @param array $tags ['tag_name']
     * @return array ['tag_name'=> 'tag_id']
     */
    public function batchAddTags(array $tags)
    {
        $data = [];
        foreach ($tags as $tag_name) {
            $tag_model = self::findOne(['name' => $tag_name]);

            if (!$tag_model) {
                $tag_model = new TagEntity();
                $tag_model->name = $tag_name;
                $tag_model->save();
            }

            $data[$tag_name] = $tag_model->id;
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
        $model = self::find()->where(['name' => $tags])->asArray()->all();
        return $model;
    }

    public static function getQuestionTagsById($question_id)
    {

    }

    /**
     * add question tag
     * @param $user_id
     * @param $question_id
     * @param array $tag_ids
     * @throws ParamsInvalidException
     */
    public function addQuestionTag($user_id, $question_id, array $tag_ids)
    {

        if (empty($user_id) || empty($question_id) || empty($tag_ids)) {
            throw new ParamsInvalidException(['user_id', 'question_id', 'tag_ids']);
        }

        $data = [];
        $create_at = time();

        foreach ($tag_ids as $tag_id) {
            $data[] = [$question_id, $tag_id, $user_id, $create_at];
        }

        #batch add
        self::getDb()->createCommand()->batchInsert(
            'question_has_tag',
            ['question_id', 'tag_id', 'create_by', 'create_at'],
            $data
        )->execute();
    }

    /**
     * add question modify event
     * @param $event such as modify, add_tag, delete_tag ...
     * @param $question_id
     */
    public function addQuestionHistoryEvent($event, $question_id)
    {

    }

    /**
     * remove question tag
     * @param $user_id
     * @param $question_id
     * @param array $tag_ids
     */
    public function removeQuestionTag($user_id, $question_id, array $tag_ids)
    {
        self::getDb()->createCommand()->delete(
            'question_has_tag',
            [
                'create_by' => $user_id,
                'question_id' => $question_id,
                'tag_id' => $tag_ids,
            ]
        )->execute();
    }
}
