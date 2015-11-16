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
use common\entities\QuestionEntity;
use Yii;
use yii\console\Controller;

class RepairController extends Controller
{
    const PAGE_SIZE = 500;

    public function actionIndex()
    {
        $this->actionQuestionAnswerCount();
        $this->actionAnswerCommentCount();
    }

    public function actionQuestionAnswerCount()
    {
        $page_no = 1;
        $page_size = self::PAGE_SIZE;
        do {
            $limit = $page_size;
            $offset = max($page_no - 1, 0) * $page_size;

            $result = $this->dealWithQuestionCountAnswer($limit, $offset);

            $page_no++;
            sleep(1);
        } while ($result);
    }

    public function actionAnswerCommentCount()
    {
        $page_no = 1;
        $page_size = self::PAGE_SIZE;
        do {
            $limit = $page_size;
            $offset = max($page_no - 1, 0) * $page_size;

            $result = $this->dealWithAnswerCommentCount($limit, $offset);

            $page_no++;
            sleep(1);
        } while ($result);
    }


    private function dealWithQuestionCountAnswer($limit, $offset)
    {
        $data = AnswerEntity::find()->select(
            [
                'total' => 'count(1)',
                'question_id',
            ]
        )->limit($limit)->offset($offset)->groupBy('question_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $sql = [];
        foreach ($data as $item) {
            $sql[] = sprintf(
                "UPDATE `%s` SET `count_answer`='%d' WHERE id='%d';",
                QuestionEntity::tableName(),
                $item['total'],
                $item['question_id']
            );
        }

        $command = Yii::$app->db->createCommand(implode(PHP_EOL, $sql));
        //echo $command->getRawSql();
        //exit;

        if ($command->execute() !== false) {
            echo sprintf('update db SUCCESS [%s]', $command->getRawSql()), PHP_EOL;
            foreach ($data as $item) {
                $cache_key = [REDIS_KEY_QUESTION, $item['question_id']];
                if (Yii::$app->redis->hLen($cache_key) && $item['total'] != Yii::$app->redis->hGet(
                        $cache_key,
                        'count_answer'
                    )
                ) {
                    echo sprintf('update redis KEY[%s] VALUE[%s]', implode(':', $cache_key), $item['total']), PHP_EOL;
                    Yii::$app->redis->hSet($cache_key, 'count_answer', $item['total']);
                }
            }
        } else {
            echo sprintf('update db FAIL [%s]', $command->getRawSql()), PHP_EOL;
        }

        return true;
    }

    private function dealWithAnswerCommentCount($limit, $offset)
    {
        $data = AnswerCommentEntity::find()->select(
            [
                'total' => 'count(1)',
                'answer_id',
            ]
        )->limit($limit)->offset($offset)->groupBy('answer_id')->asArray()->all();

        if (empty($data)) {
            return false;
        }

        $sql = [];
        foreach ($data as $item) {
            $sql[] = sprintf(
                "UPDATE `%s` SET `count_comment`='%d' WHERE id='%d';",
                AnswerEntity::tableName(),
                $item['total'],
                $item['answer_id']
            );
        }

        $command = Yii::$app->db->createCommand(implode(PHP_EOL, $sql));
        //echo $command->getRawSql();
        //exit;

        if ($command->execute() !== false) {
            echo sprintf('update db SUCCESS [%s]', $command->getRawSql()), PHP_EOL;
            foreach ($data as $item) {
                $cache_key = [REDIS_KEY_ANSWER, $item['answer_id']];
                if (Yii::$app->redis->hLen($cache_key) && $item['total'] != Yii::$app->redis->hGet(
                        $cache_key,
                        'count_answer'
                    )
                ) {
                    echo sprintf('update redis KEY[%s] VALUE[%s]', implode(':', $cache_key), $item['total']), PHP_EOL;
                    Yii::$app->redis->hSet($cache_key, 'count_comment', $item['total']);
                }
            }
        } else {
            echo sprintf('update db FAIL [%s]', $command->getRawSql()), PHP_EOL;
        }

        return true;
    }
}