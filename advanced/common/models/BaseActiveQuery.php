<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/26
 * Time: 17:21
 */

namespace common\models;

use yii\base\Exception;
use yii\db\ActiveQuery;

class BaseActiveQuery extends ActiveQuery
{
    /**
     * 限制器
     * @param      $page_no
     * @param int  $page_size
     * @param null $max 如果设置了此项，将表示展示的最多数量，超出将报错
     * @return $this
     * @throws Exception
     */
    public function limiter($page_no, $page_size = 10, $max = null)
    {
        $limit = $page_size;
        $offset = max($page_no - 1, 0) * $page_size;

        if ($max && ($page_no * $page_size) >= $max) {
            throw new Exception(sprintf('当前页数[ %s ]已超过系统限制！', $page_no));
        }

        return $this->limit($limit)->offset($offset);
    }
}