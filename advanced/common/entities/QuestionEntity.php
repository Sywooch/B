<?php

namespace common\entities;

use common\behaviors\OperatorBehavior;
use common\behaviors\QuestionBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\helpers\StringHelper;
use common\models\Question;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * @property mixed  questionTags
 * @property string season
 */
class QuestionEntity extends Question
{

    //const EVENT_QUESTION_MODIFY = 'modify_question';

    const MAX_TAGS_NUMBERS = 8; //最多的标签数
    const MIN_TAGS_NUMBERS = 1; //最少的标签数
    const MAX_TAGS_LENGTH = 15; //标签最长的字符数，“我是1”长度为3
    const MIN_TAGS_LENGTH = 2; //标签最短的字符数，“我是1”长度为3
    const MIN_SUBJECT_LENGTH = 6;//问题长度

    const EVENT_TEST = 'question_test'; //测试事件


    const STATUS_ANONYMOUS = 'yes';
    const STATUS_UNANONYMOUS = 'no';

    const STATUS_ORIGINAL = 'original'; //原稿，初始状态
    const STATUS_REVIEW = 'review';     //审核
    const STATUS_EDITED = 'edited';     //正常
    const STATUS_RECOMMEND = 'recommend';     //正常
    const STATUS_DISABLE = 'disable';   //禁止
    const STATUS_LOCK = 'lock';         //锁定，不允许回答
    const STATUS_CRAWL = 'crawl';       //抓取

    const STATUS_DISPLAY = 'original,review,edited,lock'; //允许显示的状态
    const STATUS_DISPLAY_FOR_SPIDER = 'edited,recommend,lock'; //允许显示的状态，给搜索引擎

    public $update_reason;

    public function behaviors()
    {
        return [
            'operator'          => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                ],
            ],
            'timestamp'         => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            'question_behavior' => [
                'class' => QuestionBehavior::className(),
            ],
        ];
    }

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'common_edit' => ['update_reason', 'subject', 'tags', 'content'],
            ]
        );
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [['tags'], 'checkTagsAttribute', 'skipOnEmpty' => true];
        $rules[] = [['subject'], 'checkSubjectAttribute'];
        $rules[] = [['update_reason'], 'string', 'max' => 255, 'on' => 'common_edit'];

        return $rules;
    }

    public function checkSubjectAttribute($attribute, $params)
    {
        $subject_length = StringHelper::countStringLength($this->subject);
        if ($subject_length < self::MIN_SUBJECT_LENGTH) {
            $this->addError(
                $attribute,
                sprintf('标题：%s 字符长度不得小于 %d 字符，当前长度为：%d 字符。', $this->subject, self::MIN_SUBJECT_LENGTH, $subject_length)
            );

            return false;
        }

        return true;
    }

    /**
     * 标签检查
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkTagsAttribute($attribute, $params)
    {
        $tags = explode(',', $this->tags);
        if (count($tags) > self::MAX_TAGS_NUMBERS) {
            $this->addError($attribute, sprintf('标签不得超过 %d 个。', self::MAX_TAGS_NUMBERS));

            return false;
        } else {
            if (count($tags) < self::MIN_TAGS_NUMBERS) {
                $this->addError($attribute, sprintf('标签不得少于 %d 个。', self::MIN_TAGS_NUMBERS));

                return false;
            }
        }

        foreach ($tags as $tag) {
            $tag_length = StringHelper::countStringLength($tag);
            if ($tag_length > self::MAX_TAGS_LENGTH) {
                $this->addError(
                    $attribute,
                    sprintf('标签：%s 字符长度不得超过 %d 字符，当前长度为：%d 字符。', $tag, self::MAX_TAGS_LENGTH, $tag_length)
                );

                return false;
            } else {
                if ($tag_length < self::MIN_TAGS_LENGTH) {
                    $this->addError(
                        $attribute,
                        sprintf('标签：%s 字符长度不得短于 %d 字符，当前长度为：%d 字符。', $tag, self::MIN_TAGS_LENGTH, $tag_length)
                    );

                    return false;
                }
            }
        }

        #todo check tag status(not in disable list) | relation(auto replace correct tag)

        return true;
    }

    public function getMaxTagsNumber()
    {
        return self::MAX_TAGS_NUMBERS;
    }

    public function getMinTagsNumber()
    {
        return self::MIN_TAGS_NUMBERS;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(AnswerEntity::className(), ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowQuestions()
    {
       //todo
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(
            UserEntity::className(),
            [
                'id' => 'user_id',
            ]
        )->viaTable(
            'follow_question',
            ['follow_question_id' => 'id']
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBy()
    {
        return $this->hasOne(UserEntity::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionEventHistories()
    {
        return $this->hasMany(QuestionVersionEntity::className(), ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionInvites()
    {
        return $this->hasMany(QuestionInviteEntity::className(), ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionReviews()
    {
        return $this->hasMany(QuestionReviewEntity::className(), ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionTags()
    {
        return $this->hasMany(QuestionTagEntity::className(), ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(TagEntity::className(), ['id' => 'tag_id'])->viaTable(
            'question_tag',
            ['question_id' => 'id']
        );
    }
}
