<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 10/22
 * Time: 10:45
 */

namespace common\helpers;


use yii\i18n\Formatter;

class FormatterHelper extends Formatter
{
    public function asX($a)
    {

        return 'aaa' . $a;
    }
}