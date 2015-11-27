<?php

namespace common\entities;

use common\components\Error;
use common\helpers\StringHelper;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\behaviors\QuestionBehavior;
use common\models\Question;
use yii\db\ActiveRecord;
use Yii;

/**
 * @property mixed questionTags
 */
class QuestionEntity extends Question
{

    //const EVENT_QUESTION_MODIFY = 'modify_question';

    const MAX_TAGS_NUMBERS = 8; //最多的标签数
    const MIN_TAGS_NUMBERS = 1; //最少的标签数
    const MAX_TAGS_LENGTH = 15; //标签最长的字符数，“我是1”长度为3
    const MIN_TAGS_LENGTH = 2; //标签最短的字符数，“我是1”长度为3

    const MIN_SUBJECT_LENGTH = 6;//问题长度


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


    //
    
    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        // add some rules
        //$rules['fieldRequired'] = ['field', 'required'];
        //$rules['usernameLength']=['username', 'string', 'min' => 2, 'max' => 255];

        $rules[] = [['tags'], 'checkTagsAttribute', 'skipOnEmpty' => true];
        $rules[] = [['subject'], 'checkSubjectAttribute'];

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

    public function behaviors()
    {
        return [
            'operator'          => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                    //ActiveRecord::EVENT_BEFORE_UPDATE => 'modify_by',
                ],
            ],
            'timestamp'         => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_at', 'active_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'active_at',
                ],
            ],
            'question_behavior' => [
                'class' => QuestionBehavior::className(),
            ],
        ];
    }

    public function init()
    {
        parent::init();

        #注册事件，修改问题，当有回答用户，触发方法 $this->trigger(self::EVENT_QUESTION_MODIFY, new EventXXX($user))
        //Yii::trace('On Event ' . self::EVENT_QUESTION_MODIFY, 'event');
        //$this->on(self::EVENT_QUESTION_MODIFY, [NotificationService::className(), 'questionModify']);
    }
}
