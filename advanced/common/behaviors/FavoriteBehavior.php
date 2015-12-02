<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/26
 * Time: 16:38
 */

namespace common\behaviors;


use common\components\Counter;
use common\components\Updater;
use common\entities\FavoriteCategoryEntity;
use common\entities\FavoriteEntity;
use common\helpers\TimeHelper;
use common\services\FavoriteService;
use yii\base\Behavior;
use Yii;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;

/**
 * Class FavoriteRecordBehavior
 * @package common\behaviors
 * @property \common\entities\FavoriteEntity owner
 */
class FavoriteBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterFavoriteInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterFavoriteDelete',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterFavoriteUpdate',
        ];
    }
    
    public function afterFavoriteInsert()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $this->dealWithActiveFavorite();
        $this->dealWithAddCounter();
    }

    public function afterFavoriteDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithRemoveCounter();
    }

    public function afterFavoriteUpdate(ModelEvent $event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $old_favorite_category_id = $this->owner->getOldAttribute('favorite_category_id');

        if ($old_favorite_category_id && $this->owner->favorite_category_id != $old_favorite_category_id) {
            $this->dealWithMoveCategory($old_favorite_category_id, $this->owner->favorite_category_id);
        }
    }

    /**
     * 设置收藏夹最后活动时间及最后一次收藏的内容
     * @throws \common\exceptions\NotFoundModelException
     * @throws \yii\base\Exception
     */
    private function dealWithActiveFavorite()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = Updater::updateFavoriteCategoryActiveAt(
            $this->owner->favorite_category_id,
            TimeHelper::getCurrentTime()
        );

        Yii::trace(sprintf('Update Favorite Result: %s', $result), 'behavior');
    }

    private function dealWithAddCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = Counter::addFavorite($this->owner->favorite_category_id);

        return $result;
    }

    private function dealWithRemoveCounter()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = Counter::removeFavorite($this->owner->favorite_category_id);

        return $result;
    }


    private function dealWithMoveCategory($from_favorite_category_id, $to_favorite_category_id)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        Counter::removeFavorite($from_favorite_category_id);
        Counter::addFavorite($to_favorite_category_id);
    }
}
