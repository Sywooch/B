<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[QuestionVersion]].

 * @see QuestionEventHistory
 */
class QuestionEventHistoryQuery extends BaseActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return QuestionVersion[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return QuestionVersion|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}