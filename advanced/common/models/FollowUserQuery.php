<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[FollowUser]].
 *
 * @see FollowUser
 */
class FollowUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return FollowUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return FollowUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}