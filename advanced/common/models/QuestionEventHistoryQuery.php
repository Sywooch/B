<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[QuestionEventHistory]].
 *
 * @see QuestionEventHistory
 */
class QuestionEventHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return QuestionEventHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return QuestionEventHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}