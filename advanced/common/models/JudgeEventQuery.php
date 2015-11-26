<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[JudgeEvent]].
 *
 * @see JudgeEvent
 */
class JudgeEventQuery extends BaseActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return JudgeEvent[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return JudgeEvent|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}