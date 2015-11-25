<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 18:45
 */

namespace common\entities;


use common\behaviors\AnswerCommentBehavior;
use common\behaviors\IpBehavior;
use common\behaviors\OperatorBehavior;
use common\behaviors\TimestampBehavior;
use common\components\Error;
use common\models\AnswerComment;
use Yii;
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
            'behavior'  => [
                'class' => AnswerCommentBehavior::className(),
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

    public static function getCommentListByAnswerId($answer_id, $limit = 10, $offset = 0)
    {
        $model = self::find()->where(
            ['answer_id' => $answer_id]
        )->limit($limit)->offset($offset)->asArray()->all();

        return $model;
    }


    public static function getCommentCountByAnswerId($answer_id)
    {
        $data = AnswerEntity::getAnswerByAnswerId($answer_id);

        if (false !== $data) {
            $count = $data['count_comment'];
        } else {
            $count = 0;
        }

        return $count;
    }
}