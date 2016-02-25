<?php

namespace common\models;

use common\entities\QuestionEntity;

/**
 * This is the ActiveQuery class for [[Question]].
 *
 * @see Question
 */
class QuestionQuery extends BaseActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Question[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Question|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function answered($number = 0)
    {
        return $this->andWhere(
            'count_answer>:number',
            [
                ':number' => $number,
            ]
        );
    }

    public function orderByTime($order = 'DESC')
    {
        return $this->addOrderBy(sprintf('created_at %s', $order));
    }

    public function allowShowStatus($is_spider = false)
    {
        if ($is_spider) {
            $status = QuestionEntity::STATUS_DISPLAY_FOR_SPIDER;
        } else {
            $status = QuestionEntity::STATUS_DISPLAY;
        }

        $status = array_filter(explode(',', $status));

        return $this->andWhere(
            ['status' => $status]
        );

    }
    
    public function recent($period = 7)
    {
        return $this->andWhere(
            'created_at>=:created_at',
            [
                ':created_at' => time() - $period * 86400,
            ]
        );
    }

    public function unAnswered()
    {
        return $this->andWhere('count_answer=0');
    }
}