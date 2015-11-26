<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[JudgeAction]].
 *
 * @see JudgeAction
 */
class JudgeActionQuery extends BaseActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return JudgeAction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return JudgeAction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}