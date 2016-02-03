<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/11
 * Time: 17:50
 */

namespace console\controllers;

use common\entities\AnswerCommentEntity;
use common\entities\AnswerEntity;
use common\entities\CommentEntity;
use common\entities\FavoriteEntity;
use common\entities\FollowEntity;
use common\entities\QuestionEntity;
use common\entities\QuestionTagEntity;
use common\entities\TagEntity;
use common\entities\UserProfileEntity;
use common\entities\VoteEntity;
use common\models\AssociateModel;
use common\services\AnswerService;
use common\services\QuestionService;
use common\services\TagService;
use common\services\UserService;
use Yii;
use yii\console\Controller;

class RepairCronJobController extends Controller
{
    const PAGE_SIZE = 500;

    public function actionIndex()
    {
        $start_time = microtime(true);

        $this->actionQuestion();
        $this->actionAnswer();
        $this->actionComment();
        $this->actionUser();
        $this->actionTag();

        $end_time = microtime(true);

        echo sprintf('本次更新耗时:%ss', round($end_time - $start_time, 2));
    }

    public function actionQuestion()
    {
        //问题的回答数
        $this->actuator('QuestionAnswerCount');
        //问题的关注数
        $this->actuator('QuestionFollowCount');
        //问题的收藏数
        $this->actuator('QuestionFavoriteCount');
        //问题的喜欢
        $this->actuator('QuestionLikeCount');
        //问题的讨厌
        $this->actuator('QuestionHateCount');

    }

    public function actionAnswer()
    {
        //回答的评论数
        //$this->actuator('AnswerCommentCount');
        //回答的喜欢
        $this->actuator('AnswerLikeCount');
        //回答的讨厌
        $this->actuator('AnswerHateCount');
    }

    public function actionComment()
    {
        //评论处理
        $this->actuator('AnswerCommentCount');

    }

    public function actionUser()
    {
        //提问数
        $this->actuator('UserQuestionCount');

        //回答数
        $this->actuator('UserAnswerCount');

        //关注好友数
        $this->actuator('UserFollowUserCount');

        //粉丝数
        $this->actuator('UserFansCount');

        //关注问题数
        $this->actuator('UserFollowQuestionCount');

        //关注TAG
        $this->actuator('UserFollowTagCount');
    }

    public function actionTag()
    {
        //提问数
        $this->actuator('TagFollowCount');
        $this->actuator('TagUseCount');
    }


    /**
     * 执行器
     * @param $method
     */
    private function actuator($method)
    {
        $page_no = 1;
        $page_size = self::PAGE_SIZE;
        $method = sprintf('dealWith%s', $method);

        do {
            $limit = $page_size;
            $offset = max($page_no - 1, 0) * $page_size;
            $result = $this->$method($limit, $offset);
            $page_no++;
            usleep(10);
        } while ($result);
    }

    private function executeQuestionUpdate($update_field, $data)
    {
        $sql = [];
        foreach ($data as $item) {
            $sql[] = sprintf(
                "UPDATE `%s` SET `%s`='%d' WHERE id='%d';",
                QuestionEntity::tableName(),
                $update_field,
                $item['total'],
                $item['question_id']
            );
        }

        $command = QuestionEntity::getDb()->createCommand(implode(PHP_EOL, $sql));

        if ($command->execute() !== false) {
            echo sprintf('%s SUCCESS SQL:%s%s%s', __FUNCTION__, PHP_EOL, $command->getRawSql(), PHP_EOL);
            //更新redis cache
            foreach ($data as $item) {
                QuestionService::updateQuestionCache($item['question_id'], [$update_field => intval($item['total'])]);
            }
        } else {
            echo sprintf('FAIL [%s]%s', $command->getRawSql(), PHP_EOL);
        }
    }

    private function executeAnswerUpdate($update_field, $data)
    {
        $sql = [];
        foreach ($data as $item) {
            $sql[] = sprintf(
                "UPDATE `%s` SET `%s`='%d' WHERE id='%d';",
                AnswerEntity::tableName(),
                $update_field,
                $item['total'],
                $item['answer_id']
            );
        }

        $command = AnswerEntity::getDb()->createCommand(implode(PHP_EOL, $sql));
        //echo $command->getRawSql();
        //exit;

        if ($command->execute() !== false) {
            echo sprintf('%s SUCCESS SQL:%s%s%s', __FUNCTION__, PHP_EOL, $command->getRawSql(), PHP_EOL);
            //更新redis cache
            foreach ($data as $item) {
                AnswerService::updateAnswerCache($item['answer_id'], [$update_field => intval($item['total'])]);
            }
        } else {
            echo sprintf('FAIL [%s]%s', $command->getRawSql(), PHP_EOL);
        }
    }

    private function executeUserUpdate($update_field, $data)
    {
        $sql = [];
        foreach ($data as $item) {
            $sql[] = sprintf(
                "UPDATE `%s` SET `%s`='%d' WHERE `user_id`='%d';",
                UserProfileEntity::tableName(),
                $update_field,
                $item['total'],
                $item['user_id']
            );
        }

        $command = UserProfileEntity::getDb()->createCommand(implode(PHP_EOL, $sql));
        //echo $command->getRawSql();
        //exit;

        if ($command->execute() !== false) {
            echo sprintf('%s SUCCESS SQL:%s%s%s', __FUNCTION__, PHP_EOL, $command->getRawSql(), PHP_EOL);
            //更新redis cache
            foreach ($data as $item) {
                UserService::updateUserCache($item['user_id'], [$update_field => intval($item['total'])]);
            }
        } else {
            echo sprintf('FAIL [%s]%s', $command->getRawSql(), PHP_EOL);
        }
    }


    private function executeTagUpdate($update_field, $data)
    {
        $sql = [];
        foreach ($data as $item) {
            $sql[] = sprintf(
                "UPDATE `%s` SET `%s`='%d' WHERE `id`='%d';",
                TagEntity::tableName(),
                $update_field,
                $item['total'],
                $item['tag_id']
            );
        }

        $command = TagEntity::getDb()->createCommand(implode(PHP_EOL, $sql));
        //echo $command->getRawSql();
        //exit;

        if ($command->execute() !== false) {
            echo sprintf('%s SUCCESS SQL:%s%s%s', __FUNCTION__, PHP_EOL, $command->getRawSql(), PHP_EOL);
            //更新redis cache
            foreach ($data as $item) {
                TagService::updateTagCache($item['tag_id'], [$update_field => intval($item['total'])]);
            }
        } else {
            echo sprintf('FAIL [%s]%s', $command->getRawSql(), PHP_EOL);
        }
    }

    /////////////////////////////// question /////////////////////////////////////////////

    private function dealWithQuestionAnswerCount($limit, $offset)
    {
        $update_field = 'count_answer';
        $data = AnswerEntity::find()->select(
            [
                'total' => 'count(1)',
                'question_id',
            ]
        )->limit($limit)->offset($offset)->groupBy('question_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeQuestionUpdate($update_field, $data);

        return true;
    }

    private function dealWithQuestionFollowCount($limit, $offset)
    {
        $update_field = 'count_follow';

        $data = FollowEntity::find()->select(
            [
                'total'       => 'count(1)',
                'question_id' => 'associate_id',
            ]
        )->where(
            [
                'associate_type' => AssociateModel::TYPE_QUESTION,
            ]
        )->limit($limit)->offset($offset)->groupBy(
            'associate_id'
        )->asArray()
                            ->all();

        if (empty($data)) {
            return false;
        }

        $this->executeQuestionUpdate($update_field, $data);

        return true;
    }

    private function dealWithQuestionFavoriteCount($limit, $offset)
    {
        $update_field = 'count_favorite';

        $data = FavoriteEntity::find()->select(
            [
                'total'       => 'count(1)',
                'question_id' => 'associate_id',
            ]
        )->where(
            [
                'associate_type' => FavoriteEntity::TYPE_QUESTION,
            ]
        )->limit($limit)->offset($offset)->groupBy('associate_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeQuestionUpdate($update_field, $data);

        return true;
    }

    private function dealWithQuestionLikeCount($limit, $offset)
    {
        $update_field = 'count_like';

        $data = VoteEntity::find()->select(
            [
                'total'       => 'count(1)',
                'question_id' => 'associate_id',
            ]
        )->where(
            [
                'associate_type' => AssociateModel::TYPE_QUESTION,
                'vote'           => VoteEntity::VOTE_YES,
            ]
        )->limit($limit)->offset($offset)->groupBy('associate_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeQuestionUpdate($update_field, $data);

        return true;
    }

    private function dealWithQuestionHateCount($limit, $offset)
    {
        $update_field = 'count_hate';

        $data = VoteEntity::find()->select(
            [
                'total'       => 'count(1)',
                'question_id' => 'associate_id',
            ]
        )->where(
            [
                'associate_type' => AssociateModel::TYPE_QUESTION,
                'vote'           => VoteEntity::VOTE_NO,
            ]
        )->limit($limit)->offset($offset)->groupBy('associate_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeQuestionUpdate($update_field, $data);

        return true;
    }

    /////////////////////////////// answer /////////////////////////////////////////////


    private function dealWithAnswerCommentCount($limit, $offset)
    {
        $update_field = 'count_comment';

        $data = CommentEntity::find()->select(
            [
                'total'     => 'count(1)',
                'answer_id' => 'associate_id',
            ]
        )->where(
            [
                'associate_type' =>
                    AssociateModel::TYPE_ANSWER_COMMENT,
            ]
        )->limit($limit)->offset($offset)->groupBy(
            'associate_id'
        )->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeAnswerUpdate($update_field, $data);

        return true;
    }


    private function dealWithAnswerLikeCount($limit, $offset)
    {
        $update_field = 'count_like';

        $data = VoteEntity::find()->select(
            [
                'total'     => 'count(1)',
                'answer_id' => 'associate_id',
            ]
        )->where(
            [
                'associate_type' => AssociateModel::TYPE_ANSWER,
                'vote'           => VoteEntity::VOTE_YES,
            ]
        )->limit($limit)->offset($offset)->groupBy('associate_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeAnswerUpdate($update_field, $data);

        return true;
    }

    private function dealWithAnswerHateCount($limit, $offset)
    {
        $update_field = 'count_hate';

        $data = VoteEntity::find()->select(
            [
                'total'     => 'count(1)',
                'answer_id' => 'associate_id',
            ]
        )->where(
            [
                'associate_type' => AssociateModel::TYPE_ANSWER,
                'vote'           => VoteEntity::VOTE_NO,
            ]
        )->limit($limit)->offset($offset)->groupBy('associate_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeAnswerUpdate($update_field, $data);

        return true;
    }


    /////////////////////////////// user /////////////////////////////////////////////

    private function dealWithUserQuestionCount($limit, $offset)
    {
        $update_field = 'count_question';

        $data = QuestionEntity::find()->select(
            [
                'total'   => 'count(1)',
                'user_id' => 'created_by',
            ]
        )->limit($limit)->offset($offset)->groupBy('created_by')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeUserUpdate($update_field, $data);

        return true;
    }

    private function dealWithUserAnswerCount($limit, $offset)
    {
        $update_field = 'count_answer';

        $data = AnswerEntity::find()->select(
            [
                'total'   => 'count(1)',
                'user_id' => 'created_by',
            ]
        )->limit($limit)->offset($offset)->groupBy('created_by')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeUserUpdate($update_field, $data);

        return true;
    }

    private function dealWithUserFollowUserCount($limit, $offset)
    {
        $update_field = 'count_follow_user';

        $data = FollowEntity::find()->select(
            [
                'total'   => 'count(1)',
                'user_id' => 'associate_id',
            ]
        )->limit($limit)->offset($offset)->groupBy('user_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeUserUpdate($update_field, $data);

        return true;
    }

    private function dealWithUserFollowQuestionCount($limit, $offset)
    {
        $update_field = 'count_follow_question';

        $data = FollowEntity::find()->select(
            [
                'total' => 'count(1)',
                'user_id',
            ]
        )->where(
            [
                'associate_type' => AssociateModel::TYPE_QUESTION,
            ]
        )->limit($limit)->offset($offset)->groupBy('user_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeUserUpdate($update_field, $data);

        return true;
    }

    private function dealWithUserFollowTagCount($limit, $offset)
    {
        $update_field = 'count_follow_tag';

        $data = FollowEntity::find()->select(
            [
                'total'   => 'count(1)',
                'user_id' => 'user_id',
            ]
        )->where(['associate_type' => AssociateModel::TYPE_TAG])->limit($limit)->offset($offset)->groupBy('user_id')
                            ->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeUserUpdate($update_field, $data);

        return true;
    }

    private function dealWithUserFansCount($limit, $offset)
    {
        $update_field = 'count_fans';

        $data = FollowEntity::find()->select(
            [
                'total'   => 'count(1)',
                'user_id' => 'associate_id',
            ]
        )->where(['associate_type' => AssociateModel::TYPE_USER])->limit($limit)->offset($offset)->groupBy(
            'associate_id'
        )
                            ->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeUserUpdate($update_field, $data);

        return true;
    }

    /////////////////////////////// tag /////////////////////////////////////////////

    private function dealWithTagFollowCount($limit, $offset)
    {
        $update_field = 'count_follow';

        $data = FollowEntity::find()->select(
            [
                'total'  => 'count(1)',
                'tag_id' => 'associate_id',
            ]
        )->where(
            [
                'associate_type' => AssociateModel::TYPE_TAG,
            ]
        )->limit($limit)->offset($offset)->groupBy('associate_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeTagUpdate($update_field, $data);

        return true;
    }

    private function dealWithTagUseCount($limit, $offset)
    {
        $update_field = 'count_use';

        $data = QuestionTagEntity::find()->select(
            [
                'total'  => 'count(1)',
                'tag_id' => 'tag_id',
            ]
        )->limit($limit)->offset($offset)->groupBy('tag_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $this->executeTagUpdate($update_field, $data);

        return true;
    }
}
