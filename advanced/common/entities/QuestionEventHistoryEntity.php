<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2015/10/14
 * Time: 15:53
 */

namespace common\entities;


use common\behaviors\IpBehavior;
use common\behaviors\TimestampBehavior;
use common\models\QuestionEventHistory;
use Yii;
use yii\db\ActiveRecord;

class QuestionEventHistoryEntity extends QuestionEventHistory
{
    const QUESTION_ADD = 101;
    const QUESTION_MODIFY_SUBJECT = 102;
    const QUESTION_MODIFY_CONTENT = 103;

    const QUESTION_ADD_TAG = 111;
    const QUESTION_REMOVE_TAG = 112;

    const QUESTION_ADD_REDIRECT = 121;
    const QUESTION_MODIFY_REDIRECT = 122;

    const QUESTION_LOCK = 131;
    const QUESTION_CLOSE = 132;

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                ],
            ],
            'ip'        => [
                'class' => IpBehavior::className(),
            ],
        ];
    }


    public function addQuestion($event_content)
    {
        $allow_cancel = false;
        $this->addEvent(self::QUESTION_ADD, $event_content, $allow_cancel);
    }

    public function modifyQuestionSubject($event_content)
    {
        $allow_cancel = true;
        $this->addEvent(self::QUESTION_MODIFY_SUBJECT, $event_content, $allow_cancel);
    }

    public function modifyQuestionContent($event_content)
    {
        $allow_cancel = true;
        $this->addEvent(self::QUESTION_MODIFY_CONTENT, $event_content, $allow_cancel);
    }

    public function addQuestionRedirect($event_content)
    {
        $allow_cancel = true;
        $this->addEvent(self::QUESTION_ADD_REDIRECT, $event_content, $allow_cancel);
    }

    public function modifyQuestionRedirect($event_content)
    {
        $allow_cancel = false;
        $this->addEvent(self::QUESTION_MODIFY_REDIRECT, $event_content, $allow_cancel);
    }

    public function addTag(array $tags)
    {
        $allow_cancel = true;
        foreach ($tags as $tag) {
            $this->addEvent(self::QUESTION_ADD_TAG, $tag, $allow_cancel);
        }
    }

    public function removeTag(array $tags)
    {
        $allow_cancel = true;
        foreach ($tags as $tag) {
            $this->addEvent(self::QUESTION_REMOVE_TAG, $tag, $allow_cancel);
        }
    }

    private function addEvent($event_type, $event_content, $allow_cancel, $reason = null)
    {

        $data = [
            'question_id'   => intval($this->question_id),
            'event_type'    => $event_type,
            'event_content' => $event_content,
            'create_by'     => $this->create_by,
            'allow_cancel'  => $allow_cancel ? 'yes' : 'no',
            'reason'        => $reason,
        ];

        $model = clone $this;

        if ($model->load($data, '') && $model->save()) {
            Yii::trace(
                sprintf(
                    'Add Question History (Event: %s, Content: %s) success',
                    $event_type,
                    $event_content
                ),
                'event'
            );

            return true;
        } else {
            Yii::trace(
                sprintf(
                    'Add Question History (Event: %s, Content: %s) fail',
                    $event_type,
                    $event_content
                ),
                'event'
            );
            Yii::trace($model->getErrors(), 'event');

            return false;
        }
    }

    public function getEvent($question_id)
    {

    }

    private function formatEventData()
    {

    }
}