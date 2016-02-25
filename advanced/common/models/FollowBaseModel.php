<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 2016-01-28
 * Time: 10:20
 */

namespace common\models;


use common\components\Error;
use common\entities\FollowEntity;
use common\exceptions\ModelSaveErrorException;
use common\helpers\TimeHelper;
use Yii;
use yii\base\Model;

abstract class FollowBaseModel extends Model
{
    public $associate_type;
    public $associate_id;
    public $user_id;
    public $cache_key;

    public function __construct($associate_id, $user_id)
    {
        $this->associate_id = $associate_id;
        $this->user_id = $user_id;
    }

    public function addFollow()
    {
        if (empty($this->user_id) || empty($this->associate_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id, associate_id']);
        }

        //检查是否超出最大关注数量
        if ($this->checkWhetherOverMaxFollowNumber()) {
            return Error::set(Error::TYPE_FOLLOW_OVER_MAX_NUMBER);
        }

        $model = FollowEntity::findOne(
            [
                'user_id'        => $this->user_id,
                'associate_id'   => $this->associate_id,
                'associate_type' => $this->associate_type,
            ]
        );



        if (!$model) {
            $model = new FollowEntity;
            $model->user_id = $this->user_id;
            $model->associate_id = $this->associate_id;
            $model->associate_type = $this->associate_type;

            if ($model->save()) {
                $result = true;
            } else {
                throw new ModelSaveErrorException($model);
            }
        } else {
            $result = true;
        }

        return $result;
    }

    public function removeFollow()
    {
        if (empty($this->associate_id)) {
            return Error::set(Error::TYPE_SYSTEM_PARAMS_IS_EMPTY, ['user_id, follow_id']);
        }

        $model = FollowEntity::find()->where(
            [
                'associate_id'   => $this->associate_id,
                'associate_type' => $this->associate_type,
            ]
        )->filterWhere(['user_id' => $this->user_id])->all();

        //移除数据库数据
        foreach ($model as $item) {
            /* @var $item FollowEntity */
            $item->delete();
        }

        return true;
    }

    public function checkFollowedByCache()
    {
        $this->ensureFollowHasCached($this->cache_key, $this->associate_id);

        //使用缓存查询是否已关注
        $result = Yii::$app->redis->zScore($this->cache_key, $this->user_id);

        return $result ? true : false;
    }

    public function checkFollowedByDB()
    {
        return FollowEntity::find()->where(
            [
                'user_id'        => $this->user_id,
                'associate_id'   => $this->associate_id,
                'associate_type' => $this->associate_type,
            ]
        )->count(1);
    }

    public function ensureFollowHasCached()
    {
        if (Yii::$app->redis->zCard($this->cache_key) == 0) {
            $insert_cache_data = FollowEntity::find()->select(['created_at', 'user_id'])->where(
                [
                    'associate_id'   => $this->associate_id,
                    'associate_type' => $this->associate_type,
                ]
            )->asArray()->all();

            if ($insert_cache_data) {
                return Yii::$app->redis->batchZAdd($this->cache_key, $insert_cache_data);
            }
        }
    }


    public function removeFollowFromCache()
    {
        if (Yii::$app->redis->zScore($this->cache_key, $this->user_id) !== false) {
            //存在则移除
            return Yii::$app->redis->zRem($this->cache_key, $this->user_id);
        } else {
            return false;
        }
    }

    public function checkFollowed()
    {
        if ($this->checkWhetherUseCache()) {
            return $this->checkFollowedByCache();
        } else {
            return $this->checkFollowedByDB();
        }
    }

    public function addFollowToCache()
    {
        if (!$this->checkFollowedByCache()) {
            self::ensureFollowHasCached($this->cache_key, $this->associate_id);

            $insert_cache_data = [];
            //存在则判断是否已存在集合中
            $cache_data = Yii::$app->redis->zScore($this->cache_key, $this->user_id);

            if ($cache_data === false) {
                $insert_cache_data[] = ['create_at' => TimeHelper::getCurrentTime(), 'user_id' => $this->user_id];
            }

            //添加到缓存中
            if ($insert_cache_data) {
                return Yii::$app->redis->batchZAdd($this->cache_key, $insert_cache_data);
            }
        }

        return false;
    }

    /**
     * 检查是否使用缓存
     * @return mixed
     */
    abstract public function checkWhetherUseCache();

    /**
     * 检查是否超过最大关注数量
     * @return mixed
     */
    abstract public function checkWhetherOverMaxFollowNumber();
}
