<?php

namespace console\modules\crawler\models;

/**
 * This is the ActiveQuery class for [[Crawler]].
 *
 * @see Crawler
 */
class CrawlerQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        $this->andWhere('[[status]]="Y"');
        return $this;
    }

    /**
     * @inheritdoc
     * @return Crawler[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Crawler|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}