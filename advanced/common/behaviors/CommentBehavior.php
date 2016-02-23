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
use common\models\AssociateDataModel;
use common\models\AssociateModel;
use common\models\NoticeDataModel;
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
        //处理AT
        $this->dealWithAt();

        if ($this->owner->associate_type == AssociateModel::TYPE_ANSWER) {
            //回答添加评论数量
            Counter::answerAddComment($this->owner->associate_id);
        } else {
            //todo 其他类型
        }


        $answer_data = AnswerService::getAnswerByAnswerId($this->owner->associate_id);

        //关联数据
        $associate_data = new AssociateDataModel();
        $associate_data->answer_id = $this->owner->associate_id;
        $associate_data->comment_id = $this->owner->id;

        //通知数据
        $notice_data = new NoticeDataModel();
        $notice_data->sender = $this->owner->created_by;
        $notice_data->receiver = $answer_data->created_by;

        //触发用户事件
        Yii::$app->user->trigger(
            sprintf('event_%s_create', $this->owner->associate_type),
            new UserAssociationEvent(
                [
                    'associate_id'   => $this->owner->getAnswer()->question_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    /**
     * 评论删除后
     */
    public function eventCommentDelete()
    {
        if ($this->owner->associate_type == AssociateModel::TYPE_ANSWER) {
            //回答减少评论数量
            Counter::answerDeleteComment($this->owner->associate_id);

            $answer_data = AnswerService::getAnswerByAnswerId($this->owner->associate_id);

            //关联数据
            $associate_data = new AssociateDataModel();
            $associate_data->answer_id = $this->owner->associate_id;
            $associate_data->comment_id = $this->owner->id;

            //通知数据
            $notice_data = new NoticeDataModel();
            $notice_data->sender = $this->owner->created_by;
            $notice_data->receiver = $answer_data->created_by;
        } else {
            //todo 其他类型
        }


        //触发用户事件
        Yii::$app->user->trigger(
            sprintf('event_%s_delete', $this->owner->associate_type),
            new UserAssociationEvent(
                [
                    'associate_id'   => $this->owner->getAnswer()->question_id,
                    'associate_type' => AssociateModel::TYPE_QUESTION,
                    'associate_data' => $associate_data,
                    'notice_data'    => $notice_data,
                ]
            )
        );
    }

    /**
     * todo untest
     * @throws \yii\base\InvalidConfigException
     */
    private function dealWithAt()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $at_username = AtHelper::findAtUsername($this->owner->content);
        $at_user_ids = UserService::getUserIdByUsername($at_username);

        if ($at_user_ids) {
            //关联数据
            $associate_data = new AssociateDataModel();
            $associate_data->answer_id = $this->owner->associate_id;
            $associate_data->comment_id = $this->owner->id;

            //通知数据
            $notice_data = new NoticeDataModel();
            $notice_data->sender = $this->owner->created_by;
            $notice_data->receiver = $at_user_ids;

            //触发用户事件
            Yii::$app->user->trigger(
                sprintf('event_%s_at_sb', $this->owner->associate_type),
                new UserAssociationEvent(
                    [
                        'associate_id'   => $this->owner->getAnswer()->question_id,
                        'associate_type' => AssociateModel::TYPE_QUESTION,
                        'associate_data' => $associate_data,
                        'notice_data'    => $notice_data,
                    ]
                )
            );
        }
    }
}
