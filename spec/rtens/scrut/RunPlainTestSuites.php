<?php
namespace spec\rtens\scrut;

use watoki\factory\Factory;
use rtens\scrut\Assert;
use rtens\scrut\Failure;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\results\PassedTestResult;
use rtens\scrut\TestName;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestFilter;

class RunPlainTestSuites extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
    }

    function emptySuite() {
        $this->runTestSuite(RunPlainTestSuites_Empty::class);

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function executeMethods() {
        $suite = new PlainTestSuite(new TestFilter(), RunPlainTestSuites_Foo::class);
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 2);
        $this->assert($this->listener->started[1]->last(), 'foo');
        $this->assert($this->listener->started[2]->last(), 'bar');
    }

    function markMethodsWithoutAssertionsAsIncomplete() {
        $suite = new PlainTestSuite(new TestFilter(), RunPlainTestSuites_Incomplete::class);
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 2);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
        $this->assert->isInstanceOf($this->listener->results[1], PassedTestResult::class);
    }

    function injectMethodsArguments() {
        $this->runTestSuite(RunPlainTestSuites_Inject::class);

        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function callBeforeAndAfter() {
        $suite = new PlainTestSuite(new TestFilter(), RunPlainTestSuites_Bar::class);
        RunPlainTestSuites_Bar::reset();
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 2);
        $this->assert(RunPlainTestSuites_Bar::$beforeCalled, 2);
        $this->assert(RunPlainTestSuites_Bar::$afterCalled, 2);
    }

    function beforeMethodMustBePublic() {
        $suite = new PlainTestSuite(new TestFilter(), RunPlainTestSuites_ProtectedBefore::class);
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var \rtens\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->getFailure()->getMessage(), 'Method [' . RunPlainTestSuites_ProtectedBefore::class . '::before] must be public');
    }

    function afterMethodMustBePublic() {
        $suite = new PlainTestSuite(new TestFilter(), RunPlainTestSuites_ProtectedAfter::class);
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var \rtens\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->getFailure()->getMessage(), 'Method [' . RunPlainTestSuites_ProtectedAfter::class . '::after] must be public');
    }

    function discardParentName() {
        $suite = new PlainTestSuite(new TestFilter(), RunPlainTestSuites_Empty::class, new TestName("Foo"));
        $suite->run($this->listener);

        $this->assert($this->listener->started[0]->toString(), RunPlainTestSuites_Empty::class);
    }

    /**
     * @param $class
     */
    private function runTestSuite($class) {
        $suite = new PlainTestSuite(new TestFilter(), $class);
        $suite->run($this->listener);
    }
}

class RunPlainTestSuites_Empty {

}

class RunPlainTestSuites_Foo {

    function foo() {
        throw new Failure();
    }

    function bar(Assert $assert) {
        $assert(false);
    }
}

class RunPlainTestSuites_Incomplete {

    function foo(Assert $that) {
    }

    function bar() {
    }
}

class RunPlainTestSuites_Inject {

    function before(Factory $factory) {
        $factory->setSingleton(new \DateTime('yesterday'));
    }

    function foo(RunPlainTestSuites_Empty $foo, \DateTime $time) {
        assert($foo instanceof RunPlainTestSuites_Empty);
        assert($time == new \DateTime('yesterday'));
    }

    function after(Factory $factory) {
        assert($factory->getInstance(\DateTime::class) == new \DateTime('yesterday'));
    }
}

class RunPlainTestSuites_Bar {

    public static $beforeCalled = 0;
    public static $afterCalled = 0;

    static function reset() {
        self::$beforeCalled = 0;
        self::$afterCalled = 0;
    }

    function before() {
        self::$beforeCalled++;
    }

    function after() {
        self::$afterCalled++;
    }

    function foo() {
    }

    function bar() {
        throw new Failure();
    }

}

class RunPlainTestSuites_ProtectedBefore {

    protected function before() {
    }

    function foo() {
    }
}

class RunPlainTestSuites_ProtectedAfter {

    protected function after() {
    }

    function foo() {
    }
}