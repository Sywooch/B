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
    public function batchAddTags(array $tags)
    {
        $data = [];
        foreach ($tags as $tag_name) {
            $model = self::findOne(['name' => $tag_name]);

            if (!$model) {
                $model = new TagEntity();
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

    public static function getQuestionTagsById($question_id)
    {

    }

    /**
     * add question tag
     * @param       $user_id
     * @param       $question_id
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

        return $this->addFollowTag($user_id, $tag_ids);
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
            'question_has_tag',
            [
                'create_by'   => $user_id,
                'question_id' => $question_id,
                'tag_id'      => $tag_ids,
            ]
        )->execute();

        return $result;
        //$this->removeFollowTag($user_id, $tag_ids);
    }

    public function addFollowTag($user_id, array $tag_ids)
    {
        $followTagEntity = Yii::createObject(FollowTagEntity::className());

        return $followTagEntity->addFollowTag($user_id, $tag_ids);
    }

    public function removeFollowTag($user_id, array $tag_ids)
    {
        #when user remove tag, don't remove follow tag!
    }
}
