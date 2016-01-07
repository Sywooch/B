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
    public function filterAttributes($data)
    {
        if (empty($data)) {
            return [];
        }

        $class = new ReflectionClass($this);
        $attributes = $class->getDefaultProperties();

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $attributes)) {
                $this->$key = empty($value) ? $attributes[$key] : $value;
            }
        }

        return $this;
    }
}
