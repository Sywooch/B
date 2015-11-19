<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/19
 * Time: 10:27
 */

namespace common\models\xunsearch;

use common\helpers\ArrayHelper;
use hightman\xunsearch\ActiveRecord;

class BaseXunSearch extends ActiveRecord
{
    const WORD_CLASS = 'n,an,nr,ns,nt,nz'; #词性

    public function fenci($word, $limit = 5)
    {
        $result = parent::getDb()->getScws()->getTops($word, $limit, self::WORD_CLASS);

        if ($result) {
            $result = ArrayHelper::getColumn($result, 'word');
        }

        return $result;
    }
}