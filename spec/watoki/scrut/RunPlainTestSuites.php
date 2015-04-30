<?php
namespace watoki\scrut;

use watoki\factory\Factory;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\tests\plain\PlainTestSuite;
use watoki\scrut\tests\statics\StaticTestSuite;

class RunPlainTestSuites extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
    }

    function emptySuite() {
        $suite = new PlainTestSuite(new Factory(), new RunPlainTestSuites_Empty());
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function executeMethods() {
        $suite = new PlainTestSuite(new Factory(), new RunPlainTestSuites_Foo());
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 2);
        $this->assert($this->listener->started[1]->last(), 'foo');
        $this->assert($this->listener->started[2]->last(), 'bar');
    }

    function callBeforeAndAfter() {
        $suite = new PlainTestSuite(new Factory(), new RunPlainTestSuites_Bar());
        RunPlainTestSuites_Bar::reset();
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 2);
        $this->assert(RunPlainTestSuites_Bar::$beforeCalled, 2);
        $this->assert(RunPlainTestSuites_Bar::$afterCalled, 2);
    }

    function beforeMethodMustBePublic() {
        $suite = new PlainTestSuite(new Factory(), new RunPlainTestSuites_ProtectedBefore());
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->getFailure()->getMessage(), 'Method [watoki\\scrut\\RunPlainTestSuites_ProtectedBefore::before] must be public');
    }

    function afterMethodMustBePublic() {
        $suite = new PlainTestSuite(new Factory(), new RunPlainTestSuites_ProtectedAfter());
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var \watoki\scrut\results\FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert($result->getFailure()->getMessage(), 'Method [watoki\\scrut\\RunPlainTestSuites_ProtectedAfter::after] must be public');
    }

    function discardParentName() {
        $suite = new PlainTestSuite(new Factory(), new RunPlainTestSuites_Empty(), new TestName("Foo"));
        $suite->run($this->listener);

        $this->assert($this->listener->started[0]->toString(), RunPlainTestSuites_Empty::class);
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