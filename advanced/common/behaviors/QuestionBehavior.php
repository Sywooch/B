<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/12
 * Time: 10:25
 */

namespace common\behaviors;

use common\entities\FollowQuestionEntity;
use common\entities\QuestionEntity;
use common\entities\TagEntity;
use common\services\NotificationService;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class QuestionContentBehavior
 * @package common\behaviors
 * @property \common\entities\QuestionEntity owner
 */
class QuestionBehavior extends Behavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterQuestionInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterQuestionUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterQuestionDelete',
        ];
    }

    public function afterQuestionInsert($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        //$this->dealWithTags();
        //$this->dealWithCounter();
        //$this->dealWithAttachments();
        $this->dealWithAddFollowQuestion();

    }

    public function afterQuestionUpdate($event)
    {
        $owner = $this->owner;
        Yii::trace('Process ' . __FUNCTION__, 'behavior');


        $this->dealWithTags();
        //$this->dealWithAttachments();

    }

    public function afterQuestionDelete($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithRemoveFollowQuestion();

        # delete notify if operator is not delete by others.
        if ($this->owner->create_by != Yii::$app->user->id) {
            NotificationService::questionDelete($this->owner->create_by, $this->owner->id);
        }
    }

    /**
     * add question follow
     */
    public function dealWithAddFollowQuestion()
    {
        $model = Yii::createObject(FollowQuestionEntity::className());
        $model->addFollow($this->owner->id, $this->owner->create_by);
    }

    public function dealWithRemoveFollowQuestion()
    {
        $model = Yii::createObject(FollowQuestionEntity::className());
        $model->removeFollow($this->owner->id);
    }

    /**
     * @var $tag_model \common\entities\TagEntity
     */
    public function dealWithTags()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $owner = $this->owner;


        $new_tags = $owner->tags ? explode(',', $owner->tags) : [];
        $old_tags = $add_tags = $remove_tags = [];

        #print_r($this->owner);exit;
        $tags = $this->owner->questionTags;

        //var_dump($tags);
        //exit;
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $old_tags[] = $tag->name;
            }

            $add_tags = array_diff($new_tags, $old_tags);
            $remove_tags = array_diff($old_tags, $new_tags);
        } else {
            $add_tags = $new_tags;
        }

        //print_r($add_tags);exit;


        if ($add_tags) {
            $tag_model = Yii::createObject(TagEntity::className());

            $tag_relation = $tag_model->batchAddTags($add_tags);
            if ($tag_relation) {
                $tag_ids = array_values($tag_relation);
                $tag_model->addQuestionTag($this->owner->create_by, $this->owner->id, $tag_ids);
            }


            #todo check whether need to log
            $tag_model->addQuestionHistoryEvent('add_tag',  $this->owner->id);
        }

        if ($remove_tags) {
            $tag_model = Yii::createObject(TagEntity::className());
            $tag_relation = $tag_model->batchGetTagIds($remove_tags);


            $tag_ids = ArrayHelper::getColumn($tag_relation, 'id');
            if ($tag_ids) {
                $tag_model->removeQuestionTag($this->owner->create_by, $this->owner->id, $tag_ids);
            }

            #todo check whether need to log
            $tag_model->addQuestionHistoryEvent('remove_tag', $this->owner->id);

        }

        //print_r($owner->tags);

    }

    public function dealWithCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }

    /**
     *
     */
    public function dealWithAttachments()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $owner = $this->owner;

        print_r($owner->content);

    }

}