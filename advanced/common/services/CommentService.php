<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 14:58
 */

namespace common\services;

use common\components\Error;
use common\components\Updater;
use common\entities\AnswerCommentEntity;
use common\exceptions\ModelSaveErrorException;
use common\models\CacheAnswerCommentModel;
use Yii;

class CommentService extends BaseService
{
    public static function addAnswerComment(
        $answer_id,
        $user_id,
        $content,
        $is_anonymous = AnswerCommentEntity::STATUS_UNANONYMOUS
    ) {
        if (empty($answer_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['answer_id']);
        }

        if (empty($user_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id']);
        }

        if (empty($content)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['content']);
        }

        $model = new AnswerCommentEntity;

        $data = [
            'associate_type' => AssociateModel::TYPE_ANSWER_COMMENT,
            'associate_id'   => $answer_id,
            'content'        => $content,
            'is_anonymous'   => $is_anonymous,
        ];

        if ($model->load($data, '') && $model->save()) {
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
        $is_anonymous = AnswerCommentEntity::STATUS_UNANONYMOUS
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

        $model = AnswerCommentEntity::findOne(['id' => $comment_id, 'created_at' => $user_id]);

        if ($model->load(['content' => $content, 'is_anonymous' => $is_anonymous,], '') && $model->save()) {
            return true;
        } else {
            throw new ModelSaveErrorException($model);
        }
    }

    /**
     * @param $id
     * @return array|AnswerCommentEntity
     */
    public static function getAnswerCommentByCommentId($id)
    {
        $model = AnswerCommentEntity::find()->where(
            ['id' => $id]
        )->asArray()->one();

        $cache_answer_comment_model = new CacheAnswerCommentModel();
        $data = $cache_answer_comment_model->filter($model);

        //todo 是否需要做缓存？

        return $cache_answer_comment_model->build($data);
    }

    public static function getCommentListByAnswerId($answer_id, $limit = 10, $offset = 0)
    {
        $model = AnswerCommentEntity::find()->where(
            ['answer_id' => $answer_id]
        )->limit($limit)->offset($offset)->asArray()->all();

        return $model;
    }


    public static function getCommentCountByAnswerId($answer_id)
    {
        $data = AnswerService::getAnswerByAnswerId($answer_id);

        if (false !== $data) {
            $count = $data['count_comment'];
        } else {
            $count = 0;
        }

        return $count;
    }

    public static function setAnonymous($id)
    {
        return Updater::setAnswerCommentAnonymous($id);
    }

    public static function cancelAnonymous($id)
    {
        return Updater::cancelAnswerCommentAnonymous($id);
    }
}