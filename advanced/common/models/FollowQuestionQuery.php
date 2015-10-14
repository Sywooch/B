<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[FollowQuestion]].
 *
 * @see FollowQuestion
 */
class FollowQuestionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return FollowQuestion[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return FollowQuestion|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}