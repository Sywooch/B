<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/21
 * Time: 16:01
 */

namespace common\services;


use common\components\Judger;
use common\entities\AnswerEntity;
use common\entities\AnswerCommentEntity;
use Yii;

class AnswerService extends BaseService
{
    public $answer;
    
    public function __construct(AnswerEntity $answer)
    {
        $this->answer = $answer;
    }
    
    public function foldAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            $this->answer->foldAnswer($answer_id, $user_id);
        }
    }

    public function cancelFoldAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            $this->answer->cancelFoldAnswer($answer_id, $user_id);
        }
    }

    public function anonymousAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            $this->answer->anonymousAnswer($answer_id, $user_id);
        }
    }

    public function cancelAnonymousAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            $this->answer->cancelAnonymousAnswer($answer_id, $user_id);
        }
    }

    public function agreeAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            $this->answer->agreeAnswer($answer_id, $user_id);
        }
    }

    public function cancelAgreeAnswer($answer_id, $user_id)
    {
        if (Judger::checkNeedToJudge($user_id)) {

        } else {
            $this->answer->cancelAgreeAnswer($answer_id, $user_id);
        }
    }

    public function addComment($answer_id, $user_id, $content, $is_anonymous)
    {
        /* @var $answerCommentEntity AnswerCommentEntity */
        $answerCommentEntity = Yii::createObject(AnswerCommentEntity::className());
        $answerCommentEntity->addComment($answer_id, $user_id, $content, $is_anonymous);
    }
}