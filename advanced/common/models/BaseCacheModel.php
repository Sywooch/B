<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/24
 * Time: 14:32
 */

namespace common\models;


use yii\base\Model;

class BaseCacheModel extends Model
{
    public function filterAttributes($data)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        $attributes = parent::attributes();

        $result = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $attributes)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}