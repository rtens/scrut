<?php
namespace watoki\scrut;

use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\tests\plain\PlainTestSuite;
use watoki\scrut\tests\statics\StaticTestSuite;

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
        $suite = new PlainTestSuite(RunPlainTestSuites_Foo::class);
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 2);
        $this->assert($this->listener->started[1]->last(), 'foo');
        $this->assert($this->listener->started[2]->last(), 'bar');
    }

    function markMethodsWithoutAssertionsAsIncomplete() {
        $suite = new PlainTestSuite(RunPlainTestSuites_Incomplete::class);
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
        $suite = new PlainTestSuite(RunPlainTestSuites_Bar::class);
        RunPlainTestSuites_Bar::reset();
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 2);
        $this->assert(RunPlainTestSuites_Bar::$beforeCalled, 2);
        $this->assert(RunPlainTestSuites_Bar::$afterCalled, 2);
    }

    function beforeMethodMustBePublic() {
        $suite = new PlainTestSuite(RunPlainTestSuites_ProtectedBefore::class);
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->getFailure()->getMessage(), 'Method [watoki\\scrut\\RunPlainTestSuites_ProtectedBefore::before] must be public');
    }

    function afterMethodMustBePublic() {
        $suite = new PlainTestSuite(RunPlainTestSuites_ProtectedAfter::class);
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->getFailure()->getMessage(), 'Method [watoki\\scrut\\RunPlainTestSuites_ProtectedAfter::after] must be public');
    }

    function discardParentName() {
        $suite = new PlainTestSuite(RunPlainTestSuites_Empty::class, new TestName("Foo"));
        $suite->run($this->listener);

        $this->assert($this->listener->started[0]->toString(), RunPlainTestSuites_Empty::class);
    }

    /**
     * @param $class
     */
    private function runTestSuite($class) {
        $suite = new PlainTestSuite($class);
        $suite->run($this->listener);
    }
}

class RunPlainTestSuites_Empty {

}

class RunPlainTestSuites_Foo {

    function foo() {
        throw new Failure();
    }

    function bar(Asserter $assert) {
        $assert(false);
    }
}

class RunPlainTestSuites_Incomplete {

    function foo(Asserter $that) {
    }

    function bar() {
    }
}

class RunPlainTestSuites_Inject {

    function foo(RunPlainTestSuites_Empty $foo) {
        assert($foo instanceof RunPlainTestSuites_Empty);
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