<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/14
 * Time: 14:48
 */

namespace common\services;

use common\components\Error;
use common\config\RedisKey;
use common\entities\VoteEntity;
use common\exceptions\ModelSaveErrorException;
use common\exceptions\NotFoundModelException;
use Yii;

class VoteService extends BaseService
{
    const MAX_VOTE_COUNT_BY_USING_CACHE = 1000;
    
    public static function addQuestionVote($question_id, $user_id, $vote)
    {
        return self::addVote(VoteEntity::TYPE_QUESTION, $question_id, $user_id, $vote);
    }

    public static function addAnswerVote($answer_id, $user_id, $vote)
    {
        return self::addVote(VoteEntity::TYPE_ANSWER, $answer_id, $user_id, $vote);
    }

    public static function addAnswerCommentVote($answer_comment_id, $user_id, $vote)
    {
        return self::addVote(VoteEntity::TYPE_ANSWER_COMMENT, $answer_comment_id, $user_id, $vote);
    }

    public static function addArticleVote($question_id, $user_id, $vote)
    {
        return self::addVote(VoteEntity::TYPE_ARTICLE, $question_id, $user_id, $vote);
    }

    public static function addVote($type, $associate_id, $user_id, $vote)
    {
        if (!$associate_id || !$user_id || !$vote) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_ERROR, ['associate_id, user_id, vote']);
        }

        $model = new VoteEntity();
        $model->associate_type = $type;
        $model->associate_id = $associate_id;
        $model->created_by = $user_id;
        $model->vote = $vote;

        if ($model->save()) {
            return true;
        } else {
            throw new ModelSaveErrorException($model);
        }
    }

    public static function updateQuestionVote($question_id, $user_id, $vote)
    {
        return self::updateVote(VoteEntity::TYPE_QUESTION, $question_id, $user_id, $vote);
    }

    public static function updateAnswerVote($answer_id, $user_id, $vote)
    {
        return self::updateVote(VoteEntity::TYPE_ANSWER, $answer_id, $user_id, $vote);
    }

    public static function updateAnswerCommentVote($answer_comment_id, $user_id, $vote)
    {
        return self::updateVote(VoteEntity::TYPE_ANSWER_COMMENT, $answer_comment_id, $user_id, $vote);
    }

    public static function updateArticleVote($question_id, $user_id, $vote)
    {
        return self::updateVote(VoteEntity::TYPE_ARTICLE, $question_id, $user_id, $vote);
    }

    public static function updateVote($type, $associate_id, $user_id, $vote)
    {
        if (!$associate_id || !$user_id) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_ERROR, ['associate_id, user_id']);
        }
        /* @var $model VoteEntity */
        $model = VoteEntity::find()->where(
            [
                'associate_type' => $type,
                'associate_id'   => $associate_id,
                'created_by'     => $user_id,
            ]
        )->one();

        if (!$model) {
            throw new NotFoundModelException('vote', implode(':', [$associate_id, $user_id, $type]));
        }

        //var_dump($model->vote , $vote);exit;
        //如果存在，并且两次投票不同，则删除
        if ($model->vote != $vote) {
            $model->vote = $vote;
            if ($model->save()) {
                return true;
            } else {
                throw new ModelSaveErrorException($model);
            }
        }

        return true;
    }


    public static function addUserOfVoteQuestionCache($associate_id, $user_id, $vote)
    {
        return self::addUserOfVoteCache(VoteEntity::TYPE_QUESTION, $associate_id, $user_id, $vote);
    }

    public static function addUserOfVoteAnswerCache($associate_id, $user_id, $vote)
    {
        return self::addUserOfVoteCache(VoteEntity::TYPE_ANSWER, $associate_id, $user_id, $vote);
    }

    /**
     * @param $associate_type
     * @param $associate_id
     * @param $user_id
     * @param $vote //在缓存中,YES=0,NO=1
     * @return mixed
     * @throws NotFoundModelException
     * @throws \Exception
     */
    public static function addUserOfVoteCache($associate_type, $associate_id, $user_id, $vote)
    {
        switch ($associate_type) {
            case VoteEntity::TYPE_QUESTION:
                $model = QuestionService::getQuestionByQuestionId($associate_id);
                $cache_key = [RedisKey::REDIS_KEY_QUESTION_VOTE_USER_LIST, $associate_id];
                break;
            case VoteEntity::TYPE_ANSWER:
                $model = AnswerService::getAnswerByAnswerId($associate_id);
                $cache_key = [RedisKey::REDIS_KEY_ANSWER_VOTE_USER_LIST, $associate_id];
                break;
            case VoteEntity::TYPE_ANSWER_COMMENT:
                $model = CommentService::getAnswerCommentByCommentId($associate_id);
                $cache_key = [RedisKey::REDIS_KEY_ANSWER_COMMENT_VOTE_USER_LIST, $associate_id];
                break;
            default:
                throw new \Exception(sprintf('%s todo!', $associate_type));
        }


        if (!$model) {
            throw new NotFoundModelException($associate_type, $associate_id);
        }

        $insert_cache_data = [];


        if ($model['count_like'] == 0 && $model['count_hate'] == 0) {
            $insert_cache_data[$user_id] = $vote;
        } elseif (($model['count_like'] + $model['count_hate']) < self::MAX_VOTE_COUNT_BY_USING_CACHE) {
            //小于1000，则使用缓存，大于，则不处理。

            if (Yii::$app->redis->zCard($cache_key) == 0) {
                //判断是否已存在集合缓存，不存在
                $data = VoteEntity::find()->select(['vote', 'created_by'])->where(
                    [
                        'associate_type' => $associate_type,
                        'associate_id'   => $associate_id,
                    ]
                )->asArray()->all();
                foreach ($data as $item) {
                    $insert_cache_data[$item['created_by']] = $item['vote'];
                }
            } else {
                //存在则判断是否已存在集合中
                $cache_data = Yii::$app->redis->zScore($cache_key, $user_id);

                //zScore 结果为false则为不存在此缓存,或者存在缓存，但不相等
                if ($cache_data === false || ($vote == VoteEntity::VOTE_YES && $cache_data == 1) || ($vote == VoteEntity::VOTE_NO && $cache_data == 0)) {
                    $insert_cache_data[$user_id] = $vote;
                }
            }
        }

        if ($insert_cache_data) {
            //添加到缓存中.
            $score_params = [
                $cache_key,
            ];

            foreach ($insert_cache_data as $user_id => $vote) {
                if ($vote == VoteEntity::VOTE_YES) {
                    $vote = 0;
                } else {
                    $vote = 1;
                }
                $score_params[] = $vote;
                $score_params[] = $user_id;
            }

            return call_user_func_array([Yii::$app->redis, 'zAdd'], $score_params) !== false;
        } else {
            return false;
        }
    }


    public static function removeUserOfVoteQuestionCache($associate_id, $user_id)
    {
        return self::removeUserOfVoteCache(VoteEntity::TYPE_QUESTION, $associate_id, $user_id);
    }

    public static function removeUserOfVoteAnswerCache($associate_id, $user_id)
    {
        return self::removeUserOfVoteCache(VoteEntity::TYPE_ANSWER, $associate_id, $user_id);
    }

    public static function removeUserOfVoteCache($associate_type, $associate_id, $user_id)
    {
        switch ($associate_type) {
            case VoteEntity::TYPE_QUESTION:
                $model = QuestionService::getQuestionByQuestionId($associate_id);
                $cache_key = [RedisKey::REDIS_KEY_QUESTION_VOTE_USER_LIST, $associate_id];
                break;
            case VoteEntity::TYPE_ANSWER:
                $model = AnswerService::getAnswerByAnswerId($associate_id);
                $cache_key = [RedisKey::REDIS_KEY_ANSWER_VOTE_USER_LIST, $associate_id];
                break;
            default:
                throw new \Exception(sprintf('%s todo!', $associate_type));
        }

        if (!$model) {
            throw new NotFoundModelException($associate_type, $associate_id);
        }

        return Yii::$app->redis->zRem($cache_key, $user_id);
    }

    public static function deleteAnswerCommentVote($answer_comment_id, $user_id)
    {
        $query = VoteEntity::find()->where(
            [
                'associate_type' => VoteEntity::TYPE_ANSWER_COMMENT,
                'associate_id'   => $answer_comment_id,
                'created_by'     => $user_id,
            ]
        )->one();

        if ($query) {
            return $query->delete();
        } else {
            return false;
        }
    }

    public static function getUseQuestionVoteStatus($associate_id, $user_id)
    {
        return self::getUseVoteStatus(VoteEntity::TYPE_QUESTION, $associate_id, $user_id);
    }

    public static function getUseAnswerVoteStatus($associate_id, $user_id)
    {
        return self::getUseVoteStatus(VoteEntity::TYPE_ANSWER, $associate_id, $user_id);
    }

    public static function getUseAnswerCommentVoteStatus($associate_id, $user_id)
    {
        return self::getUseVoteStatus(VoteEntity::TYPE_ANSWER_COMMENT, $associate_id, $user_id);
    }

    /**
     * @param $associate_type
     * @param $associate_id
     * @param $user_id
     * @return bool|int false:没有投票; 1:反对票; 0:赞成票
     * @throws NotFoundModelException
     * @throws \Exception
     */
    public static function getUseVoteStatus($associate_type, $associate_id, $user_id)
    {
        switch ($associate_type) {
            case VoteEntity::TYPE_QUESTION:
                $model = QuestionService::getQuestionByQuestionId($associate_id);
                $cache_key = [RedisKey::REDIS_KEY_QUESTION_VOTE_USER_LIST, $associate_id];
                break;
            case VoteEntity::TYPE_ANSWER:
                $model = AnswerService::getAnswerByAnswerId($associate_id);
                $cache_key = [RedisKey::REDIS_KEY_ANSWER_VOTE_USER_LIST, $associate_id];
                break;
            case VoteEntity::TYPE_ANSWER_COMMENT:
                $model = CommentService::getAnswerCommentByCommentId($associate_id);
                $cache_key = [RedisKey::REDIS_KEY_ANSWER_COMMENT_VOTE_USER_LIST, $associate_id];
                break;
            default:
                throw new \Exception(sprintf('%s todo!', $associate_type));
        }

        if (!$model) {
            throw new NotFoundModelException($associate_type, $associate_id);
        }

        if ($model['count_like'] == 0 && $model['count_hate'] == 0) {
            $result = false;
        } elseif ($model['count_like'] + $model['count_hate'] < self::MAX_VOTE_COUNT_BY_USING_CACHE) {
            //小于1000，则使用缓存。
            if (Yii::$app->redis->zCard($cache_key) == 0) {
                //判断集合缓存是否已存在，不存在，创建缓存
                $data = VoteEntity::find()->select(['created_by', 'vote'])->where(
                    [
                        'associate_type' => $associate_type,
                        'associate_id'   => $associate_id,
                    ]
                )->asArray()->all();

                $insert_cache_data = [];
                foreach ($data as $item) {
                    $insert_cache_data[$item['created_by']] = $item['vote'];
                }

                //先添加到缓存
                if ($insert_cache_data) {
                    $sset_params = [
                        $cache_key,
                    ];

                    foreach ($insert_cache_data as $created_by => $vote) {
                        if ($vote == VoteEntity::VOTE_YES) {
                            $vote = 0;
                        } else {
                            $vote = 1;
                        }
                        $sset_params[] = $vote;
                        $sset_params[] = $created_by;
                    }

                    call_user_func_array([Yii::$app->redis, 'zAdd'], $sset_params);
                }
            }

            //zScore 结果为false则为不存在此缓存
            $vote = Yii::$app->redis->zScore($cache_key, $user_id);

            if ($vote === false) {
                $result = false;
            } else {
                $result = ($vote == 0) ? VoteEntity::VOTE_YES : VoteEntity::VOTE_NO;
            }
        } else {
            //大于则查询数据库
            $vote = VoteEntity::find()->select('vote')->where(
                [
                    'associate_type' => $associate_type,
                    'associate_id'   => $associate_id,
                    'created_by'     => $user_id,
                ]
            )->scalar();

            if ($vote) {
                $result = $vote;
            } else {
                $result = false;
            }
        }

        return $result;
    }
}
