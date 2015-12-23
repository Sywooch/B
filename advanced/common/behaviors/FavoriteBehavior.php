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
use common\entities\FavoriteEntity;
use common\helpers\TimeHelper;
use common\services\FavoriteService;
use Yii;
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
        //增加收藏缓存
        if ($this->owner->associate_type == FavoriteEntity::TYPE_QUESTION) {
            //文章收藏
            $result = FavoriteService::addUserOfFavoriteQuestionCache(
                $this->owner->associate_id,
                $this->owner->created_by
            );
            if ($result) {
                //更新文章被收藏数
                Counter::questionAddFavorite($this->owner->associate_id);
            }

        }
        //todo 其他类型的收藏

        //更新收藏夹最后更新时间
        Updater::updateFavoriteCategoryActiveAt(
            $this->owner->favorite_category_id,
            TimeHelper::getCurrentTime()
        );

        //更新收藏夹下的收藏数量
        Counter::favoriteCagetoryAddFavorite($this->owner->favorite_category_id);
    }

    public function afterFavoriteDelete()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        //移除收藏缓存
        if ($this->owner->associate_type == FavoriteEntity::TYPE_QUESTION) {
            //文章收藏
            $result = FavoriteService::removeUserOfFavoriteQuestionCache(
                $this->owner->associate_id,
                $this->owner->created_by
            );

            if ($result) {
                //更新文章被收藏数
                Counter::questionCancelFavorite($this->owner->associate_id);
            }
        }

        //todo 其他类型的收藏

        //减少收藏夹下的收藏数量
        Counter::favoriteCagetoryRemoveFavorite($this->owner->favorite_category_id);
    }

    public function afterFavoriteUpdate()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $old_favorite_category_id = $this->owner->getOldAttribute('favorite_category_id');

        //如果是移动了收藏夹，需要调整收藏夹的收藏数量
        if ($old_favorite_category_id && $this->owner->favorite_category_id != $old_favorite_category_id) {
            $this->dealWithMoveCategory($old_favorite_category_id, $this->owner->favorite_category_id);
        }
    }

    private function dealWithMoveCategory($from_favorite_category_id, $to_favorite_category_id)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        Counter::favoriteCagetoryRemoveFavorite($from_favorite_category_id);
        Counter::favoriteCagetoryAddFavorite($to_favorite_category_id);
    }
}
