<?php

namespace common\models;

use Yii;

/**
 * This is the ActiveQuery class for [[Answer]].
 *
 * @see Answer
 */
class AnswerQuery extends BaseActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Answer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Answer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


}