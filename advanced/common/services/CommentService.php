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
use common\entities\CommentEntity;
use common\exceptions\ModelSaveErrorException;
use common\models\AssociateModel;
use common\models\CacheCommentModel;
use Yii;

class CommentService extends BaseService
{
    public static function addAnswerComment(
        $answer_id,
        $user_id,
        $content,
        $is_anonymous = CommentEntity::STATUS_UNANONYMOUS
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

        $model = new CommentEntity;

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
        $is_anonymous = CommentEntity::STATUS_UNANONYMOUS
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

        $model = CommentEntity::findOne(['id' => $comment_id, 'created_at' => $user_id]);

        if ($model->load(['content' => $content, 'is_anonymous' => $is_anonymous,], '') && $model->save()) {
            return true;
        } else {
            throw new ModelSaveErrorException($model);
        }
    }

    /**
     * @param $id
     * @return array|CommentEntity
     */
    public static function getCommentByCommentId($id)
    {
        $model = CommentEntity::find()->where(
            ['id' => $id]
        )->asArray()->one();

        $cache_model = new CacheCommentModel();
        $data = $cache_model->filter($model);

        //todo 是否需要做缓存？

        return $cache_model->build($data);
    }

    /**
     * 获取回答评论列表
     * @param     $answer_id
     * @param int $limit
     * @param int $offset
     * @return \common\models\CacheCommentModel
     */
    public static function getCommentListByAnswerId($answer_id, $limit = 10, $offset = 0)
    {
        return self::getCommentList(AssociateModel::TYPE_ANSWER_COMMENT, $answer_id, $limit, $offset);
    }

    /**
     * 获取专栏评论列表
     * @param     $blog_id
     * @param int $limit
     * @param int $offset
     * @return \common\models\CacheCommentModel
     */
    public static function getCommentListByBlogId($blog_id, $limit = 10, $offset = 0)
    {
        return self::getCommentList(AssociateModel::TYPE_BLOG_COMMENT, $blog_id, $limit, $offset);
    }

    /**
     * @param     $associate_type
     * @param     $associate_id
     * @param int $limit
     * @param int $offset
     * @return array|\common\models\CacheCommentModel
     */
    private static function getCommentList($associate_type, $associate_id, $limit = 10, $offset = 0)
    {
        $model = CommentEntity::find()->where(
            [
                'associate_type' => $associate_type,
                'associate_id'   => $associate_id,
            ]
        )->limit($limit)->offset($offset)->asArray()->all();

        $cache_model = new CacheCommentModel();
        $result = [];
        foreach ($model as $item) {
            $data = $cache_model->filter($item);
            $result[$data['id']] = $cache_model->build($data);
        }

        return $result;
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