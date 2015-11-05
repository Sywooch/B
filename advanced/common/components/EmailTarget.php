<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/5
 * Time: 10:40
 */

namespace common\components;


use yii\log\EmailTarget as BaseEmailTarget;
use Yii;

class EmailTarget extends BaseEmailTarget
{
    public function init()
    {
        //Yii::trace('开始发日志邮件', 'log');
        parent::init();
    }
    public function export()
    {
        //Yii::trace('开始发日志邮件', 'log');
        parent::export();
    }
}