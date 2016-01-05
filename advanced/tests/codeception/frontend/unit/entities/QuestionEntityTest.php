<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 12/29
 * Time: 19:47
 */
namespace tests\codeception\frontend\entities;

use common\entities\QuestionEntity;
use common\services\UserService;
use tests\codeception\frontend\unit\entities\BaseEntityTest;
use Yii;

class QuestionEntityTest extends BaseEntityTest
{
    protected function _before()
    {

    }

    protected function _after()
    {

    }

    public function testUser()
    {
        $this->specify(
            '验证用户是否已登陆',
            function () {
                expect('登陆成功', $this->autoLogin())->true();
                expect('当前用户已登陆', Yii::$app->user->isGuest)->false();
            }
        );
    }

    /**
     * @depends testUser
     */
    public function testCreate()
    {
        $this->autoLogin();
        $old_user = UserService::getUserById(Yii::$app->user->id);

        $this->specify(
            '创建问题',
            function () {
                $question = new QuestionEntity();
                $question->subject = 'abc';
                $question->tags = '测试标签';
                $question->created_by = Yii::$app->user->id;

                expect('标题长度不够', $question->validate())->false();
                $question->subject = 'abc123456789';
                expect('检验通过', $question->validate())->true();
                expect('问题创建通过', $question->save())->true();
            }
        );

        $this->specify(
            '用户信息变更',
            function () use ($old_user) {
                $new_user = UserService::getUserById(Yii::$app->user->id);

                expect('问题总数+1', $new_user['count_question'])->equals($old_user['count_question'] + 1);
                expect('关注总数+1', $new_user['count_follow_question'])->equals($old_user['count_follow_question'] + 1);
            }
        );

        $this->specify(
            '校验问题创建后',
            function () {
                $question = new QuestionEntity();
                $question->subject = 'abc';
                $question->tags = '测试标签';
                $question->created_by = Yii::$app->user->id;

                expect('标题长度不够', $question->validate())->false();
                $question->subject = 'abc123456789';
                expect('检验通过', $question->validate())->true();
                expect('创建通过', $question->save())->true();

            }
        );
    }
}
