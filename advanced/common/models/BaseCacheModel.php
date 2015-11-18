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
    public function filterAttributes($data)
    {
        if (empty($data) || !is_array($data)) {
            return [];
        }

        $attributes = $this->attributes();

        $result = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $attributes)) {
                $result[$key] =empty($value)? $attributes[$key] : $value;
            }
        }

        return $result;
    }

    public function attributes()
    {
        $class = new ReflectionClass($this);
        return $class->getDefaultProperties();
    }
}