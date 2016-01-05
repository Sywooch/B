<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 19:12
 */

namespace common\behaviors;


use common\components\Counter;
use common\components\Notifier;
use common\entities\AnswerEntity;
use common\entities\NotificationEntity;
use common\helpers\AtHelper;
use common\services\AnswerService;
use common\services\NotificationService;
use common\services\UserService;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class AnswerCommentBehavior
 * @package common\behaviors
 * @property \common\entities\AnswerCommentEntity owner
 */
class AnswerCommentBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterAnswerCommentInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterAnswerCommentDelete',
        ];
    }
    
    public function afterAnswerCommentInsert($event)
    {
        //通知
        $this->dealWithNotification();
        //处理AT
        $this->dealWithAt();
        //计数
        $this->dealWithCounter();
    }

    /**
     * 评论删除后
     */
    public function afterAnswerCommentDelete()
    {
        //回答减少评论数量
        Counter::answerDeleteComment($this->owner->answer_id);
    }

    private function dealWithNotification()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $answer_data = AnswerService::getAnswerByAnswerId($this->owner->answer_id);
        if ($answer_data && isset($answer_data['created_by'])) {
            Notifier::build()->from($this->owner->created_by)->to($answer_data['created_by'])->notice(
                NotificationService::TYPE_MY_ANSWER_HAS_NEW_COMMENT,
                [
                    'question_id' => $this->owner->answer_id,
                    'answer_id'   => $answer_data['id'],
                ]
            );
        }
    }

    /**
     * todo untest
     * @throws \yii\base\InvalidConfigException
     */
    private function dealWithAt()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $username = AtHelper::findAtUsername($this->owner->content);

        if ($username) {
            $user_ids = UserService::getUserIdByUsername($username);

            $result = Notifier::build()->from(Yii::$app->user->id)->to($user_ids)->notice(
                NotificationService::TYPE_COMMENT_AT_ME,
                [
                    'user_id' => $this->owner->id,
                ]
            );
        }
    }

    private function dealWithCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //回答添加评论数量
        Counter::answerAddComment($this->owner->answer_id);
    }
}