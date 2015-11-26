<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[FollowTag]].
 *
 * @see FollowTag
 */
class FollowTagQuery extends BaseActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return FollowTag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return FollowTag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}