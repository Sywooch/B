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
use yii\base\Behavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class FavoriteRecordBehavior
 * @package common\behaviors
 * @property \common\entities\FavoriteRecordEntity owner
 */
class FavoriteRecordBehavior extends Behavior
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
        $this->dealWithCounter(1);
    }

    public function afterFavoriteRecordDelete($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $this->dealWithCounter(-1);
    }

    public function dealWithActiveFavorite()
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = false;

        $content = $this->owner->getFavoriteContent($this->owner->id);
        $favorite = FavoriteEntity::findOne($this->owner->favorite_id);

        if ($favorite) {
            $favorite->active_at = time();
            $favorite->last_favorite_content = $content;
            $result = $favorite->save();
        }

        Yii::trace(sprintf('Update Favorite Result: %s', $result), 'behavior');
    }

    public function dealWithCounter($value)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
        $result = Counter::build()->set(
            FavoriteEntity::tableName(),
            $this->owner->favorite_id
        )->value('count_favorite', $value)->execute();

        Yii::trace(sprintf('Counter Result: %s', $result), 'behavior');

        return $result;
    }
}