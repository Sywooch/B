<?php

namespace common\entities;


use common\components\Updater;
use common\helpers\StringHelper;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
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

    public static function getQuestionByQuestionId($question_id)
    {
        return self::findOne($question_id);
    }

    /**
     * @param $question_id
     * @return $this
     */
    public function getQuestionTagsByQuestionId($question_id)
    {
        /*return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable(
            'question_has_tag',
            ['question_id' => 'id']
        );*/

        $sql = 'select t.id, t.name
                from `question_has_tag` ht
                left join `tag` t
                on t.id=ht.tag_id
                where ht.question_id=:question_id
                ';

        return self::getDb()->createCommand($sql, [':question_id' => $question_id])->queryAll();
    }

    /**
     * @param $id
     * @param $content
     * @return int
     * @throws \yii\db\Exception
     */
    public function updateContent($id, $content)
    {
        Updater::build()->priority()->table(self::tableName())->set(['content' => $content])->where(
            ['id' => $id]
        )->execute();
    }

    public function updateActiveAt($id, $active_at)
    {
        Updater::build()->priority()->table(self::tableName())->set(['active_at' => $active_at])->where(
            ['id' => $id]
        )->execute();
    }

    public static function fetchCount($type, $is_spider)
    {
        switch ($type) {
            case 'latest':
                $count = self::find()->allowShowStatus($is_spider)->orderByTime()->count(1);
                break;

            case 'hot':
                $count = self::find()->allowShowStatus($is_spider)->recent()->answered()->orderByTime()->count(1);
                break;

            case 'unAnswer':
                $count = self::find()->allowShowStatus($is_spider)->unAnswered()->orderByTime()->count(1);
                break;

            default:
                $count = 0;
        }


        #总数不得超过 1000
        return min($count, 1000);
    }
    
    public static function fetchLatest($limit = 10, $offset = 0, $is_spider = false)
    {
        $cache_key = [REDIS_KEY_QUESTION_BLOCK, 'LATEST_' . $is_spider];
        $cache_data = Yii::$app->redis->get($cache_key);

        if ($cache_data === false) {
            $model = self::find()->allowShowStatus($is_spider)->orderByTime()->limit($limit)->offset(
                $offset
            )->asArray()->all();
            $cache_data = $model;

            if ($cache_data) {
                Yii::$app->redis->set($cache_key, $cache_data);
            }
        }

        return $cache_data;
    }

    public static function fetchHot($limit = 10, $offset = 0, $is_spider = false, $period = 7)
    {
        $cache_key = [REDIS_KEY_QUESTION_BLOCK, 'HOT_' . $is_spider];
        $cache_data = Yii::$app->redis->get($cache_key);

        if (!$cache_data) {
            $model = self::find()->answered(3)->allowShowStatus($is_spider)->recent()->answered()->orderByTime()->limit(
                $limit
            )->offset($offset)->asArray()->all();

            $cache_data = $model;

            if ($cache_data) {
                Yii::$app->redis->set($cache_key, $cache_data);
            }
        }

        return $cache_data;
    }

    public static function fetchUnAnswer($limit = 10, $offset = 0, $is_spider = false, $period = 7)
    {
        $cache_key = [REDIS_KEY_QUESTION_BLOCK, 'UNANSWER_' . $is_spider];
        $cache_data = Yii::$app->redis->get($cache_key);

        if (!$cache_data) {
            $model = self::find()->allowShowStatus($is_spider)->recent()->unAnswered()->orderByTime()->limit(
                $limit
            )->offset($offset)->asArray()->all();

            $cache_data = $model;

            if ($cache_data) {
                Yii::$app->redis->set($cache_key, $cache_data);
            }
        }

        return $cache_data;
    }
    
    public static function getSimilarQuestion(array $tags)
    {
        
    }
}
