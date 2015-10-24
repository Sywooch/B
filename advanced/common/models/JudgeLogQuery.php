<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[JudgeLog]].
 *
 * @see JudgeLog
 */
class JudgeLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return JudgeLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return JudgeLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}