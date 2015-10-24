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

    public function addComment($answer_id, $user_id, $content, $is_anonymous = self::STATUS_UNANONYMOUS)
    {
        if (empty($answer_id)) {
            throw new ParamsInvalidException(['answer_id']);
        }

        if (empty($user_id)) {
            throw new ParamsInvalidException(['user_id']);
        }

        if (empty($content)) {
            throw new ParamsInvalidException(['content']);
        }

        if ($this->load(
                [
                    'answer_id'    => $answer_id,
                    'content'      => $content,
                    'is_anonymous' => $is_anonymous,
                    'answer_id'    => $answer_id,
                ],
                ''
            ) && $this->save()
        ) {

            return true;
        } else {
            return false;
        }
    }

    public function modifyComment($comment_id, $answer_id, $user_id, $content, $is_anonymous = self::STATUS_UNANONYMOUS)
    {
        if (empty($comment_id)) {
            throw new ParamsInvalidException(['comment_id']);
        }

        if (empty($answer_id)) {
            throw new ParamsInvalidException(['answer_id']);
        }

        if (empty($user_id)) {
            throw new ParamsInvalidException(['user_id']);
        }

        if (empty($content)) {
            throw new ParamsInvalidException(['content']);
        }

        $model = $this->findOne(['id' => $comment_id, 'create_at' => $user_id]);

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
}