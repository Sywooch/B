<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Question]].
 *
 * @see Question
 */
class QuestionQuery extends \yii\db\ActiveQuery
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
}