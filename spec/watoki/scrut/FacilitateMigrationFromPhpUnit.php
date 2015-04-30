<?php
namespace watoki\scrut;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\tests\migration\PhpUnitTestSuite;
use watoki\scrut\tests\statics\StaticTestSuite;

/**
 * In order to facilitate migration, there should be a subclass of StaticTestSuite that
 * is compatible with PHPUnit_Framework_Assert.
 */
class FacilitateMigrationFromPhpUnit extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
    }

    function callHooks() {
        $suite = new PhpUnitCompatibility_Foo();
        $suite->run($this->listener);

        $this->assert(PhpUnitCompatibility_Foo::$calledSetUp, 1);
        $this->assert(PhpUnitCompatibility_Foo::$calledTearDown, 1);
    }

    function runSuite() {
        $suite = new PhpUnitCompatibility_Bar();
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 4);
        $this->assert($this->listener->started[1]->last(), 'testThis');
        $this->assert($this->listener->started[2]->last(), 'TestThat');
        $this->assert($this->listener->started[3]->last(), 'andThis');

        $this->assert->size($this->listener->results, 3);
        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
        /** @var FailedTestResult $result */
        $result = $this->listener->results[1];
        $this->assert->isInstanceOf($result, FailedTestResult::class);
        $this->assert->contains($result->getFailure()->getFailureMessage(), "Caught [PHPUnit_Framework_ExpectationFailedException]");
        $this->assert($result->getFailure()->getMessage(), "Failed asserting that false is true.");
    }

    function callTestSuiteHooks() {
        // Implement when needed
        $this->pass();
    }

    function dataProviders() {
        // Implement when needed
        $this->pass();
    }
}

class PhpUnitCompatibility_Foo extends PhpUnitTestSuite {

    public static $calledSetUp = 0;
    public static $calledTearDown = 0;

    protected function setUp() {
        self::$calledSetUp++;
    }

    protected function tearDown() {
        self::$calledTearDown++;
    }

    public function testFoo() {
    }
}

class PhpUnitCompatibility_Bar extends PhpUnitTestSuite {

    public function testThis() {
    }

    public function TestThat() {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertTrue(false);
    }

    /**
     * @test
     */
    public function andThis() {
    }

    public function butNotThis() {
    }
}