<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/16
 * Time: 17:00
 */

namespace common\entities;


use common\behaviors\AnswerBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\Answer;
use yii\db\ActiveRecord;

class AnswerEntity extends Answer
{
    const STATUS_FOLD = 'yes';
    const STATUS_UNFOLD = 'no';
    const STATUS_ANONYMOUS = 'yes';
    const STATUS_UNANONYMOUS = 'no';

    public $reason;

    public function behaviors()
    {
        return [
            'operator'        => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                    //ActiveRecord::EVENT_BEFORE_UPDATE   => 'modify_by',
                ],
            ],
            'timestamp'       => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                    //ActiveRecord::EVENT_BEFORE_UPDATE => 'modify_at',
                ],
            ],
            'answer_behavior' => [
                'class' => AnswerBehavior::className(),
            ],
        ];
    }


    public function foldAnswer($answer_id, $user_id)
    {
        $model = self::findOne(['answer_id' => $answer_id]);
        if ($model) {
            $model->is_fold = self::STATUS_FOLD;
            $model->save();
        }
    }

    public function cancelFoldAnswer($answer_id, $user_id)
    {
        $model = self::findOne(['answer_id' => $answer_id]);
        if ($model) {
            $model->is_fold = self::STATUS_UNFOLD;
            $model->save();
        }
    }

    public function anonymousAnswer($answer_id, $user_id)
    {
        $model = self::findOne(['answer_id' => $answer_id]);
        if ($model) {
            $model->is_anonymous = self::STATUS_ANONYMOUS;
            $model->save();
        }
    }

    public function cancelAnonymousAnswer($answer_id, $user_id)
    {
        $model = self::findOne(['answer_id' => $answer_id]);
        if ($model) {
            $model->is_anonymous = self::STATUS_UNANONYMOUS;
            $model->save();
        }
    }

    public function findAtUsers($content)
    {
        preg_match_all("/(\S*)\@([^\r\n\s]*)/i", $content, $at_list_tmp);
        $users = [];
        foreach ($at_list_tmp[2] as $key => $value) {
            if ($at_list_tmp[1][$key] || strlen($value) > 25) {
                continue;
            }
            $users[] = $value;
        }

        return ArrayHelper::map(UserEntity::find()->where(['username' => $users])->all(), 'id', 'username');
    }

    public function replace($content)
    {
        preg_match_all("/\#(\d*)/i", $content, $floor);
        if (isset($floor[1])) {
            foreach ($floor[1] as $key => $value) {
                $search = "#{$value}楼";
                $place = "[{$search}](#comment{$value}) ";
                $content = str_replace($search . ' ', $place, $content);
            }
        }

        $users = $this->findAtUsers($content);
        foreach ($users as $key => $value) {
            $search = '@' . $value;
            $url = Url::to(['/user/default/show', 'username' => $value]);
            $place = "[{$search}]({$url}) ";
            $content = str_replace($search . ' ', $place, $content);
        }

        return $content;
    }

    public static function getAnswerUserIdsByQuestionId($question_id, $limit = 100)
    {
        $sql = "
                SELECT
                  GROUP_CONCAT(
                    CONCAT(
                      a.`create_by`,
                      ',',
                      ac.`create_by`
                    )
                  )
                FROM
                  `answer` a
                  LEFT JOIN `answer_comment` ac
                    ON a.`id` = ac.`answer_id`
                WHERE a.`question_id` =:question_id
                ORDER BY a.`create_at` DESC, ac.`create_at` DESC
                LIMIT :limit ;
                ";
        $command = self::getDb()->createCommand(
            $sql,
            [
                ':question_id' => $question_id,
                ':limit'       => $limit,
            ]
        );

        $data = $command->queryAll();

        if ($data) {
            $data = array_unique(array_filter(explode(',', $data)));
        }

        return $data;
    }

    public function addAnswer($data)
    {
        if ($this->load($data) && $this->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function checkWhetherHasAnswered($question_id, $user_id)
    {
        return self::find()->select('id')->where(
            [
                'question_id' => $question_id,
                'create_by'   => $user_id,
            ]
        )->scalar();
    }
}