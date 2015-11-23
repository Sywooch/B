<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/23
 * Time: 9:56
 */

namespace common\components\rbac;

use yii\rbac\Rule;

class AuthorRule extends Rule
{
    public $name = 'isAuthor';

    public function execute($user, $item, $params)
    {
        return isset($params) ? $params->created_by == $user->id : false;
    }
}
