<?php
namespace tests\codeception\frontend;

class HelloWorldTest extends \Codeception\TestCase\Test
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

    // tests
    public function testMe()
    {
        $this->assertTrue(2 == 2);
        $this->assertTrue(2 == 2);

    }
}
