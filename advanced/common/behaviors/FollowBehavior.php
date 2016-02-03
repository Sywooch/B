<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-01-27
 * Time: 11:35
 */

namespace common\behaviors;

use common\components\Counter;
use common\components\Notifier;
use common\models\AssociateModel;
use common\services\FollowService;
use common\services\NotificationService;
use common\services\QuestionService;
use common\services\UserService;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * Class FollowBehavior
 * @package common\behaviors
 * @property \common\entities\FollowEntity owner
 */
class FollowBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterFollowInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterFollowDelete',
        ];
    }

    public function afterFollowInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');


        switch ($this->owner->associate_type) {
            case AssociateModel::TYPE_QUESTION:
                //关注此问题的人，更新缓存
                $result = FollowService::addUserOfFollowQuestionCache(
                    $this->owner->associate_id,
                    $this->owner->user_id
                );
                Yii::trace('增加关注此问题的用户缓存,结果:' . var_export($result, true), 'behavior');

                //关注问题后，更新问题被关注的数量，不依赖于是否添加缓存成功
                Counter::userAddFollowQuestion($this->owner->user_id);
                Counter::questionAddFollow($this->owner->associate_id);

                //关注问题通知
                $question = QuestionService::getQuestionByQuestionId($this->owner->associate_id);
                Notifier::build()->from($this->owner->user_id)
                        ->to($question->created_by)
                        ->where(
                            [
                                AssociateModel::TYPE_QUESTION,
                                $this->owner->associate_id,
                            ],
                            [
                                'question_id' => $this->owner->associate_id,
                            ]
                        )
                        ->notice(NotificationService::TYPE_QUESTION_BE_FOLLOWED);

                break;
            case AssociateModel::TYPE_TAG:

                $result = FollowService::addUserOfFollowTagCache(
                    $this->owner->associate_id,
                    $this->owner->user_id
                );
                Yii::trace('增加关注此标签的用户缓存:' . var_export($result, true), 'behavior');

                //关注问题后，更新标签被关注的数量
                Counter::userAddFollowTag($this->owner->user_id);
                Counter::tagAddFollow($this->owner->associate_id);

                break;
            case AssociateModel::TYPE_USER:

                $result = FollowService::addUserFansCache($this->owner->associate_id, $this->owner->user_id);

                Yii::trace('增加关注此用户的用户缓存:' . var_export($result, true), 'behavior');

                //关注用户后
                Counter::userAddFollowUser($this->owner->user_id);
                Counter::userAddFans($this->owner->associate_id);

                //关注用户通知
                $user = UserService::getUserById($this->owner->associate_id);
                Notifier::build()->from($this->owner->user_id)
                        ->to($user->id)
                        ->where(
                            [
                                AssociateModel::TYPE_USER,
                                $user->id,
                            ],
                            [
                                'user_id' => $user->id,
                            ]
                        )
                        ->notice(NotificationService::TYPE_USER_BE_FOLLOWED);
                break;
            default:
                throw new Exception(sprintf('关注 %s 关联类型 %s 未定义', $this->owner->associate_type, __FUNCTION__));
        }


    }

    public function afterFollowDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        switch ($this->owner->associate_type) {
            case AssociateModel::TYPE_QUESTION:

                //关注此问题的人，更新缓存，此方法顺序必须比 dealWithCounter 先执行
                $result = FollowService::removeUserOfFollowQuestionCache(
                    $this->owner->associate_id,
                    $this->owner->user_id
                );
                Yii::trace('移除关注此问题的用户缓存:' . var_export($result, true), 'behavior');

                //关注问题后，更新问题被关注的数量，不依赖于是否添加缓存成功
                Counter::userCancelFollowQuestion($this->owner->user_id);
                Counter::questionCancelFollow($this->owner->associate_id);

                break;
            case AssociateModel::TYPE_TAG:

                $result = FollowService::removeUserOfFollowTagCache(
                    $this->owner->associate_id,
                    $this->owner->user_id
                );

                Yii::trace('移除关注此标签的用户缓存:' . var_export($result, true), 'behavior');

                //关注问题后，更新标签被关注的数量
                Counter::userCancelFollowTag($this->owner->user_id);
                Counter::tagCancelFollow($this->owner->associate_id);

                break;
            case AssociateModel::TYPE_USER:

                $result = FollowService::removeUserFansCache(
                    $this->owner->associate_id,
                    $this->owner->user_id
                );

                Yii::trace('取消关注此用户的用户缓存:' . var_export($result, true), 'behavior');

                //取消关注用户后
                Counter::userCancelFollowUser($this->owner->user_id);
                Counter::userCancelFans($this->owner->associate_id);
                break;
            default:
                throw new Exception(sprintf('关注 %s 关联类型 %s 未定义', $this->owner->associate_type, __FUNCTION__));
        }
    }
}
