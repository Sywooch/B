<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/14
 * Time: 14:48
 */

namespace common\services;

use common\components\Error;
use common\entities\FavoriteEntity;
use common\entities\PrivateMessageEntity;
use common\entities\VoteEntity;
use common\exceptions\NotFoundModelException;
use Yii;
use yii\helpers\Json;

class VoteService extends BaseService
{
    const MAX_VOTE_COUNT_BY_USING_CACHE = 1000;
    
    public static function addQuestionVote($question_id, $user_id, $vote)
    {
        if (!$question_id || !$user_id || !$vote) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_ERROR, ['question_id, user_id, vote']);
        }
        $model = new VoteEntity();
        $model->type = VoteEntity::TYPE_QUESTION;
        $model->associate_id = $question_id;
        $model->created_by = $user_id;
        $model->vote = $vote;

        if ($model->save()) {
            return true;
        } else {
            Yii::error(sprintf('保存投票记录出错。%s', var_export($model->getErrors(), true)), __FUNCTION__);

            return false;
        }
    }

    public static function updateQuestionVote($question_id, $user_id, $vote)
    {
        if (!$question_id || !$user_id) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_ERROR, ['question_id, user_id']);
        }
        /* @var $model VoteEntity */
        $model = VoteEntity::find()->where(
            [
                'type'         => VoteEntity::TYPE_QUESTION,
                'associate_id' => $question_id,
                'created_by'   => $user_id,
            ]
        )->one();

        if (!$model) {
            throw new NotFoundModelException('vote', implode(':', [$question_id, $user_id, VoteEntity::TYPE_QUESTION]));
        }

        //var_dump($model->vote , $vote);exit;
        //如果存在，并且两次投票不同，则删除
        if ($model->vote != $vote) {
            $model->vote = $vote;
            if ($model->save()) {
                return true;
            } else {
                Yii::error(sprintf('保存投票记录出错。%s', var_export($model->getErrors(), true)), __FUNCTION__);
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
     * @param $type
     * @param $associate_id
     * @param $user_id
     * @param $vote 在缓存中,YES=0,NO=1
     * @return mixed
     * @throws NotFoundModelException
     * @throws \Exception
     */
    public static function addUserOfVoteCache($type, $associate_id, $user_id, $vote)
    {
        switch ($type) {
            case VoteEntity::TYPE_QUESTION:
                $model = QuestionService::getQuestionByQuestionId($associate_id);
                $cache_key = [REDIS_KEY_QUESTION_VOTE_USER_LIST, $associate_id];
                break;
            case VoteEntity::TYPE_ANSWER:
                $model = AnswerService::getAnswerByAnswerId($associate_id);
                $cache_key = [REDIS_KEY_ANSWER_VOTE_USER_LIST, $associate_id];
                break;
            default:
                throw new \Exception(sprintf('%s todo!', $type));
        }


        if (!$model) {
            throw new NotFoundModelException($type, $associate_id);
        }

        $insert_cache_data = [];


        if ($model['count_like'] == 0 && $model['count_hate'] == 0) {
            $insert_cache_data[$user_id] = $vote;
        } elseif (($model['count_like'] + $model['count_hate']) < self::MAX_VOTE_COUNT_BY_USING_CACHE) {
            //小于1000，则使用缓存，大于，则不处理。

            if (Yii::$app->redis->zCard($cache_key) == 0) {
                //判断是否已存在集合缓存，不存在
                $data = VoteEntity::find()->select(['created_by', 'vote'])->where(
                    [
                        'type'         => $type,
                        'associate_id' => $associate_id,
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

    public static function removeUserOfVoteCache($type, $associate_id, $user_id)
    {
        switch ($type) {
            case VoteEntity::TYPE_QUESTION:
                $model = QuestionService::getQuestionByQuestionId($associate_id);
                $cache_key = [REDIS_KEY_QUESTION_VOTE_USER_LIST, $associate_id];
                break;
            case VoteEntity::TYPE_ANSWER:
                $model = AnswerService::getAnswerByAnswerId($associate_id);
                $cache_key = [REDIS_KEY_ANSWER_VOTE_USER_LIST, $associate_id];
                break;
            default:
                throw new \Exception(sprintf('%s todo!', $type));
        }

        if (!$model) {
            throw new NotFoundModelException($type, $associate_id);
        }

        return Yii::$app->redis->zRem($cache_key, $user_id);
    }

    public static function checkUseIsVoteQuestion($associate_id, $user_id)
    {
        return self::checkUseIsVote(VoteEntity::TYPE_QUESTION, $associate_id, $user_id);
    }

    public static function checkUseIsVoteAnswer($associate_id, $user_id)
    {
        return self::checkUseIsVote(VoteEntity::TYPE_ANSWER, $associate_id, $user_id);
    }

    /**
     * todo 此方法返回有值导致是否已投票出错
     * @param $type
     * @param $associate_id
     * @param $user_id
     * @return bool|int false 没有投票，1:反对票 0:赞成票
     * @throws NotFoundModelException
     * @throws \Exception
     */
    public static function checkUseIsVote($type, $associate_id, $user_id)
    {
        switch ($type) {
            case VoteEntity::TYPE_QUESTION:
                $model = QuestionService::getQuestionByQuestionId($associate_id);
                $cache_key = [REDIS_KEY_QUESTION_VOTE_USER_LIST, $associate_id];
                break;
            case VoteEntity::TYPE_ANSWER:
                $model = AnswerService::getAnswerByAnswerId($associate_id);
                $cache_key = [REDIS_KEY_ANSWER_VOTE_USER_LIST, $associate_id];
                break;
            default:
                throw new \Exception(sprintf('%s todo!', $type));
        }

        if (!$model) {
            throw new NotFoundModelException($type, $associate_id);
        }

        if ($model['count_like'] == 0 && $model['count_hate'] == 0) {
            $result = false;
        } elseif ($model['count_like'] + $model['count_hate'] < self::MAX_VOTE_COUNT_BY_USING_CACHE) {
            if (Yii::$app->redis->zCard($cache_key) == 0) {
                //判断是否已存在集合缓存，不存在
                $data = VoteEntity::find()->select(['created_by', 'vote'])->where(
                    [
                        'type'         => $type,
                        'associate_id' => $associate_id,
                    ]
                )->asArray()->all();
                $insert_cache_data = [];
                foreach ($data as $item) {
                    $insert_cache_data[$item['created_by']] = $item['vote'];
                }

                if ($insert_cache_data) {
                    //先添加到缓存
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

                    call_user_func_array([Yii::$app->redis, 'zAdd'], $score_params);
                }
            }

            //小于1000，则使用缓存。zScore 结果为false则为不存在此缓存
            $result = Yii::$app->redis->zScore($cache_key, $user_id);
        } else {
            //大于则查询数据库
            $vote = VoteEntity::find()->select(['vote'])->where(
                [
                    'type'         => $type,
                    'associate_id' => $associate_id,
                    'created_by'   => $user_id,
                ]
            )->column(1);

            if ($vote) {
                $result = ($vote == VoteEntity::VOTE_YES) ? 0 : 1;
            } else {
                $result = false;
            }
        }

        if ($result !== false) {
            $result = (int)$result;
        }

        return $result;
    }
}
