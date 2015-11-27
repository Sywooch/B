<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/27
 * Time: 11:38
 */

namespace common\behaviors;


use common\components\Error;
use yii\db\ActiveRecord;

/**
 * Class FavoriteCategoryBehavior
 * @package common\behaviors
 * @property \common\entities\FavoriteCategoryEntity owner
 */
class FavoriteCategoryBehavior extends BaseBehavior
{
    public function events()
    {
        Yii::trace('Begin ' . $this->className(), 'behavior');

        return [
            //ActiveRecord::EVENT_AFTER_INSERT  => 'afterQuestionInsert',
            //ActiveRecord::EVENT_AFTER_UPDATE  => 'afterQuestionUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeFavoriteCategoryDelete',
            ActiveRecord::EVENT_AFTER_DELETE  => 'afterFavoriteCategoryDelete',
            //ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeQuestionSave',
            //ActiveRecord::EVENT_AFTER_FIND    => 'afterQuestionFind',
        ];
    }

    public function beforeFavoriteCategoryDelete($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');

        $favorite_count = count($this->owner->getFavorites());

        if ($favorite_count) {
            $event->isValid = false;

            return Error::set(Error::TYPE_FAVORITE_CATEGORY_DELETE_FAIL, [$favorite_count]);
        }
    }

    public function afterFavoriteCategoryDelete($event)
    {
        Yii::trace('Process ' . __FUNCTION__, 'behavior');
    }
}