<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 19:12
 */

namespace common\behaviors;


use Codeception\Step\Comment;
use common\components\Counter;
use common\components\Notifier;
use common\components\user\UserAssociationEvent;
use common\entities\UserEventLogEntity;
use common\events\CommentEvent;
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

        //触发用户动作
        CommentEvent::create($this->owner);
    }

    /**
     * 评论删除后
     */
    public function eventCommentDelete()
    {
        if ($this->owner->associate_type == AssociateModel::TYPE_ANSWER) {
            //回答减少评论数量
            Counter::answerDeleteComment($this->owner->associate_id);
        } else {
            //todo 其他类型
        }

        //触发用户动作
        CommentEvent::delete($this->owner);
    }

    /**
     * todo untest
     * @throws \yii\base\InvalidConfigException
     */
    private function dealWithAt()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');



            //触发用户动作
            CommentEvent::at($this->owner);

    }
}
