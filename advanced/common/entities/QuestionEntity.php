<?php

namespace common\entities;


use common\models\Tag;
use common\services\NotificationService;
use Yii;
use common\behaviors\IpBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\behaviors\QuestionBehavior;
use common\models\Question;
use yii\db\ActiveRecord;

/**
 * @property mixed questionTags
 */
class QuestionEntity extends Question
{

    //const EVENT_QUESTION_MODIFY = 'modify_question';

    const MAX_TAGS_NUMBERS = 5; //最多的标签数
    const MAX_TAGS_LENGTH = 30; //标签最长的字符数，汉字需要 x3

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
        return $rules;
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
        }

        foreach ($tags as $tag) {
            $tag_length = mb_strlen($tag);
            if ($tag_length > self::MAX_TAGS_LENGTH) {
                $this->addError($attribute, sprintf('标签：%s 长度不得超过%d字符，当前为%d', $tag, self::MAX_TAGS_LENGTH, $tag_length));
                return false;
            }
        }

        return true;
    }

    public function getMaxTagsLength()
    {
        return self::MAX_TAGS_LENGTH;
    }

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
            'ip' => [
                'class' => IpBehavior::className()
            ],
            'question_behavior' => [
                'class' => QuestionBehavior::className()
            ]
        ];
    }

    /*public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->create_by = Yii::$app->user->identity->getId();
            $this->create_at = time();
        }

        return true;
    }*/

    public function init()
    {
        parent::init();

        #注册事件，修改问题通知所有回答用户，触发方法：$this->trigger(self::EVENT_QUESTION_MODIFY, new EventXXX($user))
        //Yii::trace('On Event ' . self::EVENT_QUESTION_MODIFY, 'event');
        //$this->on(self::EVENT_QUESTION_MODIFY, [NotificationService::className(), 'questionModify']);
    }


    /*public function afterSave($insert, $changedAttributes)
    {

        if (parent::afterSave($insert, $changedAttributes)) {
        }

        return true;
    }*/

    /**
     * @return $this
     */
    public function getQuestionTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable(
            'question_has_tag',
            ['question_id' => 'id']
        );
    }
}
