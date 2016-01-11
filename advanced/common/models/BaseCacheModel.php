<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:32
 */

namespace common\models;

use ReflectionClass;
use yii\base\Model;

class BaseCacheModel extends Model
{
    /**
     * @param $data
     * @return BaseCacheModel
     */
    public function build($data)
    {
        if (empty($data)) {
            return null;
        }

        $class = new ReflectionClass($this);
        $attributes = $class->getDefaultProperties();

        $model = clone $this;

        foreach ($data as $key => $value) {
            $model->$key = empty($value) ? $attributes[$key] : $value;
        }

        return $model;
    }

    public function filter($data)
    {
        if (empty($data)) {
            return [];
        }

        $class = new ReflectionClass($this);
        $attributes = $class->getDefaultProperties();

        $result = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $attributes)) {
                $result[$key] = empty($value) ? $attributes[$key] : $value;
            }
        }

        return $result;
    }
}
