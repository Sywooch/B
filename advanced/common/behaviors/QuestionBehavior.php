<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/12
 * Time: 10:25
 */

namespace common\behaviors;

use common\components\Counter;
use common\components\Updater;
use common\entities\AttachmentEntity;
use common\entities\FavoriteRecordEntity;
use common\entities\FollowQuestionEntity;
use common\entities\QuestionEntity;
use common\entities\QuestionEventHistoryEntity;
use common\entities\QuestionTagEntity;
use common\entities\TagEntity;
use common\entities\UserProfileEntity;
use common\models\QuestionTag;
use common\models\xunsearch\QuestionSearch;
use common\modules\user\models\Profile;
use Yii;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class QuestionContentBehavior
 * @package common\behaviors
 * @property \common\entities\QuestionEntity owner
 */
class QuestionBehavior extends BaseBehavior
{
    public $dirtyAttributes;
    
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');
        
        return [
            ActiveRecord::EVENT_AFTER_INSERT  => 'afterQuestionInsert',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'afterQuestionUpdate',
            ActiveRecord::EVENT_AFTER_DELETE  => 'afterQuestionDelete',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeQuestionSave',
            //ActiveRecord::EVENT_AFTER_FIND    => 'afterQuestionFind',
        ];
    }

    /*public function afterQuestionFind($event)
    {

    }*/

    public function beforeQuestionValidate($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }
    
    public function beforeQuestionSave($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $this->dirtyAttributes = $this->owner->getDirtyAttributes();
        $this->dealWithTagsOrder();
    }
    
    public function afterQuestionInsert($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithAddQuestionEventHistory();
        $this->dealWithInsertTags();
        $this->dealWithUserAddQuestionCounter();
        $this->dealWithAddFollowQuestion();
        $this->dealWithAddAttachments();
        $this->dealWithRedisCacheInsert();
        $this->dealWithTagRelationCount();
    }
    
    public function afterQuestionUpdate($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithQuestionUpdateEvent();
        $this->dealWithUpdateTags();
        $this->dealWithAddAttachments();
        $this->dealWithRedisCacheUpdate();
    }
    
    public function afterQuestionDelete($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithRemoveFollowQuestion();
        $this->dealWithFavoriteRecordRemove();
        $this->dealWithUserDeleteQuestionCounter();
        #delete notify if operator is not delete by others.
    }
    
    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function dealWithAddQuestionEventHistory()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        
        /* @var $questionEventHistoryEntity QuestionEventHistoryEntity */
        $questionEventHistoryEntity = Yii::createObject(
            [
                'class'       => QuestionEventHistoryEntity::className(),
                'question_id' => $this->owner->id,
                'create_by'   => $this->owner->create_by,
            ]
        );
        
        $event_content = $this->owner->subject;
        if ($this->owner->content) {
            $event_content = implode('<decollator></decollator>', [$this->owner->subject, $this->owner->content]);
        }
        
        $result = $questionEventHistoryEntity->addQuestion($event_content);
        Yii::trace(sprintf('Add Question Event History: %s', var_export($result, true)), 'behavior');
        
        return $result;
    }
    
    /**
     * add question follow
     */
    public function dealWithAddFollowQuestion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = FollowQuestionEntity::addFollow($this->owner->id, $this->owner->create_by);
        
        Yii::trace(sprintf('Add Question Follow: %s', var_export($result, true)), 'behavior');
        
        return $result;
    }
    
    public function dealWithRemoveFollowQuestion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        /* @var $model FollowQuestionEntity */
        $model = Yii::createObject(FollowQuestionEntity::className());
        $model->removeFollow($this->owner->id);
    }
    
    public function dealWithInsertTags()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $owner = $this->owner;
        
        $new_tags = $owner->tags ? explode(',', $owner->tags) : [];
        $add_tags = $new_tags;
        
        if ($add_tags) {
            $tag_relation = TagEntity::batchAddTags($add_tags);
            if ($tag_relation) {
                $tag_ids = array_values($tag_relation);
                $result = QuestionTagEntity::addQuestionTag($this->owner->create_by, $this->owner->id, $tag_ids);

                Yii::trace(sprintf('Add Question Tag Result: %s', var_export($result, true)), 'behavior');
            }
        }
    }
    
    public function dealWithQuestionUpdateEvent()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        if ($this->dirtyAttributes) {
            /* @var $questionEventHistoryEntity QuestionEventHistoryEntity */
            $questionEventHistoryEntity = Yii::createObject(
                [
                    'class'       => QuestionEventHistoryEntity::className(),
                    'question_id' => $this->owner->id,
                    'create_by'   => $this->owner->create_by,
                ]
            );
            
            if (array_key_exists('subject', $this->dirtyAttributes)) {
                $result = $questionEventHistoryEntity->modifyQuestionSubject($this->dirtyAttributes['subject']);
                Yii::trace(sprintf('Modify　Question　Subject Result: %s', var_export($result, true)), 'behavior');
            }
            
            if (array_key_exists('content', $this->dirtyAttributes)) {
                $result = $questionEventHistoryEntity->modifyQuestionContent($this->dirtyAttributes['content']);
                Yii::trace(sprintf('Modify　Question　Content Result: %s', var_export($result, true)), 'behavior');
            }
        }
    }
    
    /**
     * @var $tag_model \common\entities\TagEntity
     */
    public function dealWithUpdateTags()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $owner = $this->owner;
        
        
        $new_tags = $owner->tags ? explode(',', $owner->tags) : [];
        $old_tags = $add_tags = $remove_tags = [];
        
        $tags = $this->owner->getQuestionTagsByQuestionId($this->owner->id);
        
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $old_tags[] = $tag['name'];
            }
            
            $add_tags = array_diff($new_tags, $old_tags);
            $remove_tags = array_diff($old_tags, $new_tags);
        } else {
            $add_tags = $new_tags;
        }
        
        /* @var $questionEventHistoryEntity QuestionEventHistoryEntity */
        $questionEventHistoryEntity = Yii::createObject(
            [
                'class'       => QuestionEventHistoryEntity::className(),
                'question_id' => $this->owner->id,
                'create_by'   => $this->owner->create_by,
            ]
        );
        
        
        if ($add_tags) {
            $tag_relation = TagEntity::batchAddTags($add_tags);
            if ($tag_relation) {
                $tag_ids = array_values($tag_relation);
                QuestionTagEntity::addQuestionTag($this->owner->create_by, $this->owner->id, $tag_ids);
            }
            
            $questionEventHistoryEntity->addTag($add_tags);
        }
        
        if ($remove_tags) {
            $tag_relation = TagEntity::batchGetTagIds($remove_tags);
            
            $tag_ids = ArrayHelper::getColumn($tag_relation, 'id');
            if ($tag_ids) {
                TagEntity::removeQuestionTag($this->owner->create_by, $this->owner->id, $tag_ids);
            }
            
            $questionEventHistoryEntity->removeTag($remove_tags);
        }
    }
    
    public function dealWithUserAddQuestionCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        
        $result = Counter::addQuestion($this->owner->create_by);

        return $result;
    }

    public function dealWithUserDeleteQuestionCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = Counter::deleteQuestion($this->owner->create_by);

        return $result;
    }
    
    /**
     * todo 未完成
     * 将临时上传的图片，转移目录，并写入attachment表
     */
    public function dealWithAddAttachments()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $owner = $this->owner;
        
        #print_r($owner->content);
        if (strpos($owner->content, AttachmentEntity::TEMP_ATTACHMENT_PATH) && preg_match_all(
                AttachmentEntity::TEMP_FILE_MATCH_REGULAR,
                $owner->content,
                $file_paths
            )
        ) {
            $search_rules = $replace_rules = [];
            
            
            foreach ($file_paths[1] as $key => $file_path) {
                $old_file_physical_path = Yii::$app->basePath . $file_path;
                
                
                if (file_exists($old_file_physical_path)) {
                    $new_file_path_without_attachment_dir = substr(
                        $file_path,
                        strlen(AttachmentEntity::TEMP_ATTACHMENT_PATH)
                    );
                    
                    $new_file_path = '/' . AttachmentEntity::ATTACHMENT_PATH . '/' . Yii::$app->user->id . $new_file_path_without_attachment_dir;
                    $new_file_physical_path = Yii::$app->basePath . $new_file_path;
                    
                    
                    $file_size = filesize($old_file_physical_path);
                    
                    $attachment = new AttachmentEntity;
                    $attachment->addQuestionAttachment(
                        $this->owner->id,
                        Yii::$app->user->id,
                        $new_file_path,
                        $file_size
                    );
                    if ($attachment->save()) {
                        $attachment->moveAttachmentFile(
                            $this->owner->id,
                            $old_file_physical_path,
                            $new_file_physical_path
                        );
                        
                        #
                        $search_rules[] = $file_path;
                        $replace_rules[] = $new_file_path;
                    }
                }
            }
            
            $this->owner->content = str_replace($search_rules, $replace_rules, $this->owner->content);
            Updater::updateContent($this->owner->id, $this->owner->content);
            
        } else {
            Yii::trace('No matching data', 'attachment');
        }
    }
    
    public function dealWithFavoriteRecordRemove()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = FavoriteRecordEntity::removeFavoriteRecord(FavoriteRecordEntity::TYPE_QUESTION, $this->owner->id);

        Yii::trace(sprintf('Remove Favorite Record Result: %s', var_export($result, true)), 'behavior');
    }

    public function dealWithXunSearch()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        try {
            $question = new QuestionSearch();
            $question->load($this->owner->getAttributes(), '');
            $question->save();
        } catch (Exception $e) {
            Yii::error(__METHOD__, 'xunsearch');
        }
    }

    public function dealWithRedisCacheInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        QuestionEntity::ensureQuestionHasCache($this->owner->id);
    }

    public function dealWithRedisCacheUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        QuestionEntity::updateQuestionCache($this->owner->id, $this->owner->getAttributes());
    }

    public function dealWithTagsOrder()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        if ($this->owner->tags) {
            $this->owner->tags = str_replace(['，', '、', ' '], ',', $this->owner->tags);
            $tags = array_unique(array_filter(explode(',', $this->owner->tags)));
            $this->owner->tags = implode(',', $tags);
        }
    }

    public function dealWithTagRelationCount()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }
}
