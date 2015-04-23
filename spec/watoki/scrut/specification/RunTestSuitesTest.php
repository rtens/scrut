<?php
namespace spec\watoki\scrut\specification;

use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\suites\DynamicTestSuite;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\Scrutinizer;

class RunTestSuitesTest extends \PHPUnit_Framework_TestCase {

    /** @var ArrayListener */
    private $listener;

    /** @var Scrutinizer */
    private $scrutinizer;

    protected function setUp() {
        $this->listener = new ArrayListener();
        $this->scrutinizer = new Scrutinizer($this->listener);
        $this->scrutinizer->listen($this->listener);
    }

    public function testNoSuites() {
        $this->scrutinizer->run();
        $this->assertEquals(0, $this->listener->count());
    }

    public function testEmptySuite() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo"));
        $this->scrutinizer->run();
        $this->assertEquals(0, $this->listener->count());
    }

    public function testSimpleSuite() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
            }
        ]));
        $this->scrutinizer->run();

        $this->assertEquals(1, $this->listener->count());
        $this->assertTrue($this->listener->hasStarted("Foo::bar"));
        $this->assertTrue($this->listener->hasFinished("Foo::bar"));
    }

    public function testSecondListener() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
            }
        ]));
        $secondListener = new ArrayListener();
        $this->scrutinizer->listen($secondListener);
        $this->scrutinizer->run();

        $this->assertEquals(1, $this->listener->count());
        $this->assertTrue($this->listener->hasStarted("Foo::bar"));
        $this->assertTrue($this->listener->hasFinished("Foo::bar"));
    }

    public function testPassingTest() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
                // Passes
            }
        ]));
        $this->scrutinizer->run();

        $this->assertInstanceOf(PassedTestResult::class, $this->listener->getResult(0));
        $this->assertInstanceOf(PassedTestResult::class, $this->listener->getResult("Foo::bar"));
    }

    public function testFailingTest() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
                throw new \Exception('Failed miserably');
            }
        ]));
        $this->scrutinizer->run();

        /** @var FailedTestResult $result */
        $result = $this->listener->getResult("Foo::bar");
        $this->assertInstanceOf(FailedTestResult::class, $result);
        $this->assertEquals("Failed miserably", $result->exception()->getMessage());
    }
}