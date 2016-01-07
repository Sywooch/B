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
use common\components\user\User;
use common\components\user\UserAssociationEvent;
use common\entities\AttachmentEntity;
use common\entities\FavoriteEntity;
use common\entities\QuestionEventHistoryEntity;
use common\entities\TagRelationEntity;
use common\helpers\TimeHelper;
use common\models\xunsearch\QuestionSearch;
use common\services\FavoriteService;
use common\services\FollowService;
use common\services\QuestionService;
use common\services\TagService;
use Yii;
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
            ActiveRecord::EVENT_AFTER_INSERT  => 'eventQuestionCreate',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'afterQuestionUpdate',
            ActiveRecord::EVENT_AFTER_DELETE  => 'afterQuestionDelete',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeQuestionSave',
        ];
    }

    public function beforeQuestionValidate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }
    
    public function beforeQuestionSave()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $this->dirtyAttributes = $this->owner->getDirtyAttributes();
        $this->dealWithTagsOrder();
    }
    
    public function eventQuestionCreate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //添加问题事件　
        $this->dealWithAddQuestionEventHistory();
        //添加问题标签
        $this->dealWithInsertTags();
        //添加用户统计
        $this->dealWithUserAddQuestionCounter();
        //添加用户关注问题
        $this->dealWithAddFollowQuestion();
        //附件处理　todo
        $this->dealWithAddAttachments();
        //生成问题缓存
        $this->dealWithInsertQuestionCache();
        //处理标签间的关注
        $this->dealWithTagRelation();
        //处理xunsearch
        $this->dealWithInsertXunSearch();
        //触发用户行为
        Yii::$app->user->trigger(
            __FUNCTION__,
            new UserAssociationEvent(
                [
                    'id'      => $this->owner->id,
                    'type'    => 'question',
                    'content' => '',
                ]
            )
        );
    }
    
    public function afterQuestionUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //处理问题事件　
        $this->dealWithQuestionUpdateEvent();
        //更新问题标签
        $this->dealWithUpdateTags();
        //更新附件
        $this->dealWithAddAttachments();
        //处理问题缓存
        $this->dealWithRedisCacheUpdate();
        //处理xunsearch
        $this->dealWithInsertXunSearch();
    }
    
    public function afterQuestionDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //删除问题事件记录
        $this->dealWithDeleteQuestionEvent();
        //移除关注者
        $this->dealWithRemoveFollowQuestion();
        //移除收藏者
        $this->dealWithFavoriteRecordRemove();
        //减少问题提问数
        $this->dealWithUserDeleteQuestionCounter();
        //删除xunsearch缓存
        $this->dealWithDeleteXunSearch();
        //删除redis缓存
        $this->dealWithDeleteQuestionCache();
        #delete notify if operator is not delete by others.
    }
    
    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    private function dealWithAddQuestionEventHistory()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        
        /* @var $questionEventHistoryEntity QuestionEventHistoryEntity */
        $questionEventHistoryEntity = Yii::createObject(
            [
                'class'       => QuestionEventHistoryEntity::className(),
                'question_id' => $this->owner->id,
                'created_by'  => $this->owner->created_by,
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
    private function dealWithAddFollowQuestion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = FollowService::addFollowQuestion($this->owner->id, $this->owner->created_by);
        Yii::trace(sprintf('Add Question Follow: %s', var_export($result, true)), 'behavior');

        return $result;
    }

    private function dealWithRemoveFollowQuestion()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = FollowService::removeFollowQuestion($this->owner->id);
        Yii::trace(sprintf('Remove Question Follow: %s', var_export($result, true)), 'behavior');

    }

    private function dealWithInsertTags()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $owner = $this->owner;
        
        $new_tags = $owner->tags ? explode(',', $owner->tags) : [];
        $add_tags = $new_tags;
        
        if ($add_tags) {
            $tag_relation = TagService::batchAddTags($add_tags);
            if ($tag_relation) {
                $tag_ids = array_values($tag_relation);
                $result = QuestionService::addQuestionTag($this->owner->created_by, $this->owner->id, $tag_ids);

                Yii::trace(sprintf('Add Question Tag Result: %s', var_export($result, true)), 'behavior');
            }
        }
    }
    
    private function dealWithQuestionUpdateEvent()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        if ($this->dirtyAttributes) {
            /* @var $questionEventHistoryEntity QuestionEventHistoryEntity */
            $questionEventHistoryEntity = Yii::createObject(
                [
                    'class'       => QuestionEventHistoryEntity::className(),
                    'question_id' => $this->owner->id,
                    'created_by'  => $this->owner->created_by,
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
    private function dealWithUpdateTags()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $owner = $this->owner;
        
        
        $new_tags = $owner->tags ? explode(',', $owner->tags) : [];
        $old_tags = $add_tags = $remove_tags = [];
        
        $tags = QuestionService::getQuestionTagsByQuestionId($this->owner->id);
        
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $old_tags[] = $tag['name'];
            }
            
            $add_tags = array_diff($new_tags, $old_tags);
            $remove_tags = array_diff($old_tags, $new_tags);
        } else {
            $add_tags = $new_tags;
        }

        //处理问题事件
        /* @var $questionEventHistoryEntity QuestionEventHistoryEntity */
        $questionEventHistoryEntity = Yii::createObject(
            [
                'class'       => QuestionEventHistoryEntity::className(),
                'question_id' => $this->owner->id,
                'created_by'  => $this->owner->created_by,
            ]
        );
        
        
        if ($add_tags) {
            $tag_relation = TagService::batchAddTags($add_tags);
            if ($tag_relation) {
                $tag_ids = array_values($tag_relation);
                QuestionService::addQuestionTag($this->owner->created_by, $this->owner->id, $tag_ids);
            }
            
            $questionEventHistoryEntity->addTag($add_tags);
        }
        
        if ($remove_tags) {
            $tag_relation = TagService::batchGetTagIds($remove_tags);
            
            $tag_ids = ArrayHelper::getColumn($tag_relation, 'id');
            if ($tag_ids) {
                QuestionService::removeQuestionTag($this->owner->id, $tag_ids);
            }
            
            $questionEventHistoryEntity->removeTag($remove_tags);
        }
    }
    
    private function dealWithUserAddQuestionCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        
        $result = Counter::userAddQuestion($this->owner->created_by);

        return $result;
    }

    private function dealWithUserDeleteQuestionCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $result = Counter::userDeleteQuestion($this->owner->created_by);

        return $result;
    }
    
    /**
     * todo 未完成
     * 将临时上传的图片，转移目录，并写入attachment表
     */
    private function dealWithAddAttachments()
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
            
            
            foreach ($file_paths[1] as $file_path) {
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
            Updater::updateQuestionContent($this->owner->id, $this->owner->content);
            
        } else {
            Yii::trace('No matching data', 'attachment');
        }
    }
    
    private function dealWithFavoriteRecordRemove()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = FavoriteService::removeFavoriteByAssociateId(FavoriteEntity::TYPE_QUESTION, $this->owner->id);

        Yii::trace(sprintf('Remove Favorite Record Result: %s', var_export($result, true)), 'behavior');
    }

    private function dealWithInsertXunSearch()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        try {
            $question = new QuestionSearch();
            $question->load($this->owner->getAttributes(), '');
            $result = $question->save();
        } catch (Exception $e) {
            $result = false;
            Yii::error(__METHOD__, 'xunsearch');
        }

        Yii::trace(sprintf('Result %s ', var_export($result, true)), 'behavior');
    }

    private function dealWithDeleteXunSearch()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        try {
            $question = new QuestionSearch;
            $question->load(['id' => $this->owner->id], '');
            $question->delete();

        } catch (Exception $e) {
            Yii::error(__METHOD__, 'xunsearch');
        }
    }

    private function dealWithInsertQuestionCache()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        QuestionService::ensureQuestionHasCache($this->owner->id);
    }

    private function dealWithDeleteQuestionCache()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        QuestionService::deleteQuestionCache($this->owner->id);
    }

    private function dealWithRedisCacheUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        QuestionService::updateQuestionCache($this->owner->id, $this->owner->getAttributes());
    }

    /**
     * 删除事件记录
     */
    private function dealWithDeleteQuestionEvent()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        QuestionEventHistoryEntity::deleteAll(
            [
                'question_id' => $this->owner->id,
            ]
        );
    }

    /**
     * 处理标签
     */
    private function dealWithTagsOrder()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        if ($this->owner->tags) {
            $this->owner->tags = str_replace(['，', '、'], ',', $this->owner->tags);
            $tags = array_unique(array_filter(explode(',', $this->owner->tags)));
            $this->owner->tags = implode(',', $tags);
        }
    }

    private function dealWithTagRelation()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $tag_names = explode(',', $this->owner->tags);

        if (count($tag_names) > 1) {
            $all_tag_name_id = TagService::getTagIdByName($tag_names);

            $all_relations = [];

            do {
                $current_item = array_shift($tag_names);
                foreach ($tag_names as $tag_name) {
                    $all_relations[$current_item][] = $tag_name;
                }

            } while (count($tag_names) > 1);


            $data = [];
            foreach ($all_relations as $tag_name_1 => $item_relation) {
                if (empty($all_tag_name_id[$tag_name_1])) {
                    continue;
                }

                $item_relation = array_unique($item_relation);
                $tag_id_1 = $all_tag_name_id[$tag_name_1];
                foreach ($item_relation as $tag_name_2) {
                    $tag_id_2 = $all_tag_name_id[$tag_name_2];
                    $data[] = [
                        $tag_id_1,
                        $tag_id_2,
                        TagRelationEntity::TYPE_RELATE,
                        1,
                        TagRelationEntity::STATUS_ENABLE,
                    ];
                }
            }

            if ($data) {
                #batch add
                $insert_sql = TagRelationEntity::getDb()->createCommand()->batchInsert(
                    TagRelationEntity::tableName(),
                    ['tag_id_1', 'tag_id_2', 'type', 'count_relation', 'status'],
                    $data
                )->getRawSql();

                //exit($insert_sql);

                $command = TagRelationEntity::getDb()->createCommand(
                    sprintf(
                        '%s ON DUPLICATE KEY UPDATE `count_relation`=`count_relation`+1;',
                        $insert_sql,
                        TimeHelper::getCurrentTime()
                    )
                );

                //$sql = $command->getRawSql();

                $result = $command->execute();

                Yii::trace(sprintf('%s Result %s ', __FUNCTION__, var_export($result, true)), 'behavior');
            }
        }
    }
}
