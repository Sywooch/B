<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/26
 * Time: 16:38
 */

namespace common\behaviors;


use common\components\Counter;
use common\entities\FavoriteEntity;
use common\entities\FavoriteRecordEntity;
use yii\base\Behavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class FavoriteRecordBehavior
 * @package common\behaviors
 * @property \common\entities\FavoriteRecordEntity owner
 */
class FavoriteRecordBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterFavoriteRecordInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterFavoriteRecordDelete',
        ];
    }

    public function afterFavoriteRecordInsert($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $this->dealWithActiveFavorite();
        $this->dealWithAddCounter();
    }

    public function afterFavoriteRecordDelete($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithRemoveCounter();
    }

    /**
     * 设置收藏夹最后活动时间及最后一次收藏的内容
     * @throws \common\exceptions\NotFoundModelException
     * @throws \yii\base\Exception
     */
    public function dealWithActiveFavorite()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = false;

        $subject = FavoriteRecordEntity::getFavoriteSubject($this->owner->id);
        $favorite = FavoriteEntity::findOne($this->owner->favorite_id);

        if ($favorite) {
            $favorite->active_at = time();
            $favorite->last_favorite_content = $subject;
            $result = $favorite->save();
        }

        Yii::trace(sprintf('Update Favorite Result: %s', $result), 'behavior');
    }

    public function dealWithAddCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = Counter::addFavorite($this->owner->favorite_id);

        return $result;
    }

    public function dealWithRemoveCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = Counter::removeFavorite($this->owner->favorite_id);

        return $result;
    }
}