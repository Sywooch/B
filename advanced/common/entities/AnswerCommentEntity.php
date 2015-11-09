<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 18:45
 */

namespace common\entities;


use common\behaviors\IpBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\exceptions\ParamsInvalidException;
use common\models\AnswerComment;
use yii\db\ActiveRecord;

class AnswerCommentEntity extends AnswerComment
{

    const STATUS_ANONYMOUS = 'yes';
    const STATUS_UNANONYMOUS = 'no';

    public function behaviors()
    {
        return [
            'operator'  => [
                'class'      => OperatorBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'create_by',
                    ActiveRecord::EVENT_BEFORE_UPDATE   => 'modify_by',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'active_at',
                ],
            ],
            'ip'        => [
                'class' => IpBehavior::className(),
            ],
            'ip'        => [
                'class' => CommentBehavior::className(),
            ],
        ];
    }

    public static function addComment($answer_id, $user_id, $content, $is_anonymous = self::STATUS_UNANONYMOUS)
    {
        if (empty($answer_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['answer_id']);
        }

        if (empty($user_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id']);
        }

        if (empty($content)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['content']);
        }

        $model = new self;

        if ($model->load(
                [
                    'answer_id'    => $answer_id,
                    'content'      => $content,
                    'is_anonymous' => $is_anonymous,
                    'answer_id'    => $answer_id,
                ],
                ''
            ) && $model->save()
        ) {

            return true;
        } else {
            return false;
        }
    }

    public static function modifyComment(
        $comment_id,
        $answer_id,
        $user_id,
        $content,
        $is_anonymous = self::STATUS_UNANONYMOUS
    ) {
        if (empty($comment_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['comment_id']);
        }

        if (empty($answer_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['answer_id']);
        }

        if (empty($user_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id']);
        }

        if (empty($content)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['content']);
        }

        $model = self::findOne(['id' => $comment_id, 'create_at' => $user_id]);

        if ($model->load(
                [
                    'content'      => $content,
                    'is_anonymous' => $is_anonymous,
                ],
                ''
            ) && $model->save()
        ) {

            return true;
        } else {
            return false;
        }
    }

    public static function getCommentListByAnswerId($answer_id)
    {
        $model = self::find()->where(
            ['answer_id' => $answer_id]
        )->asArray()->all();

        return $model;
    }
}