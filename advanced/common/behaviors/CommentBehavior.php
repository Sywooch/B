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
use common\components\user\UserAssociationEvent;
use common\entities\UserEventLogEntity;
use common\helpers\AtHelper;
use common\models\AssociateModel;
use common\services\AnswerService;
use common\services\NotificationService;
use common\services\UserService;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class CommentBehavior
 * @package common\behaviors
 * @property \common\entities\CommentEntity owner
 */
class CommentBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'eventCommentCreate',
            ActiveRecord::EVENT_AFTER_DELETE => 'eventCommentDelete',
        ];
    }

    public function eventCommentCreate()
    {
        //通知
        $this->dealWithNotification();
        //处理AT
        $this->dealWithAt();
        //计数
        $this->dealWithCounter();

        Yii::$app->user->trigger(
            sprintf('event_%s_create', $this->owner->associate_type),
            new UserAssociationEvent(
                [
                    'type' => $this->owner->associate_type,
                    'id'   => $this->owner->id,
                    'data' => [
                        'question_id' => $this->owner->getAnswer()->question_id,
                        'answer_id'   => $this->owner->associate_id,
                    ],
                ]
            )
        );
    }

    /**
     * 评论删除后
     */
    public function eventCommentDelete()
    {
        //回答减少评论数量
        Counter::answerDeleteComment($this->owner->answer_id);

        Yii::$app->user->trigger(
            sprintf('event_%s_delete', $this->owner->associate_type),
            new UserAssociationEvent(
                [
                    'type' => $this->owner->associate_type,
                    'id'   => $this->owner->id,
                    'data' => [
                        'question_id' => $this->owner->getAnswer()->question_id,
                        'answer_id'   => $this->owner->associate_id,
                    ],
                ]
            )
        );
    }

    private function dealWithNotification()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $answer_data = AnswerService::getAnswerByAnswerId($this->owner->associate_id);

        if ($answer_data && $answer_data->created_by) {
            $question_id = $this->owner->getAnswer()->question_id;
            Notifier::build()
                    ->from($this->owner->created_by)
                    ->to($answer_data->created_by)
                    ->where(
                        [
                            $this->owner->associate_type,
                            $question_id
                            ,
                        ],
                        [
                            'question_id' => $question_id,
                            'answer_id'   => $answer_data->id,
                            'comment_id'  => $this->owner->id,
                        ]
                    )
                    ->notice(NotificationService::TYPE_COMMENT_BE_CREATED_IN_ANSWER);
        }
    }

    /**
     * todo untest
     * @throws \yii\base\InvalidConfigException
     */
    private function dealWithAt()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $at_username = AtHelper::findAtUsername($this->owner->content);

        if ($at_username) {
            $at_user_ids = UserService::getUserIdByUsername($at_username);
            $question_id = $this->owner->getAnswer()->question_id;
            Notifier::build()
                    ->from($this->owner->created_by)
                    ->to($at_user_ids)
                    ->where(
                        [
                            AssociateModel::TYPE_QUESTION,
                            $question_id,
                        ],
                        [
                            'user_id'     => $this->owner->id,
                            'question_id' => $question_id,
                            'answer_id'   => $this->owner->associate_id,
                            'comment_id'  => $this->owner->id,
                        ]
                    )->notice(NotificationService::TYPE_USER_BE_AT_IN_COMMENT);
        }
    }

    private function dealWithCounter()
    {
        //todo
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //回答添加评论数量
        Counter::answerAddComment($this->owner->associate_id);
    }
}