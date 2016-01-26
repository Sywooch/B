<?php
namespace tests\codeception\frontend;


class HelloTest extends \Codeception\TestCase\Test
{
    /**
     * @var \tests\codeception\frontend\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testM1()
    {
        $this->assertTrue(true);    //传的参数不是true,断言失败
    }

    /**
     * @depends testM1
     */
    public function testM2()
    {
        $isOK = false;
        $this->assertTrue($isOK);    //你换汤不换药咋行?不还是传了个false进去?失败!
    }
}
