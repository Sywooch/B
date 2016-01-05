<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/29
 * Time: 19:47
 */
namespace tests\codeception\frontend\unit\entities;

use Codeception\Specify;
use common\services\UserService;
use tests\codeception\frontend\unit\DbTestCase;

class BaseEntityTest extends DbTestCase
{
    use Specify;

    public function autoLogin($user_id = 1)
    {
        return UserService::autoLoginById($user_id);
    }
}
