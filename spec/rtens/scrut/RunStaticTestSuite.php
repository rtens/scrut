<?php
namespace spec\rtens\scrut;

use rtens\scrut\Asserter;
use rtens\scrut\failures\EmptyTestSuiteFailure;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\results\PassedTestResult;
use rtens\scrut\TestName;
use rtens\scrut\tests\statics\StaticTestSuite;
use rtens\scrut\tests\TestFilter;

class RunStaticTestSuite extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
    }

    function emptySuite() {
        $suite = new RunStaticTestSuite_Empty(new TestFilter());
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var IncompleteTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, IncompleteTestResult::class);
        $this->assert->isInstanceOf($result->getFailure(), EmptyTestSuiteFailure::class);
    }

    function runOwnMethods() {
        $suite = new RunStaticTestSuite_Foo();
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 3);
        $this->assert->equals($this->listener->started[0]->last(), RunStaticTestSuite_Foo::class);
        $this->assert->equals($this->listener->started[1]->last(), "foo");
        $this->assert->equals($this->listener->started[2]->last(), "bar");

        $this->assert->size($this->listener->results, 2);
        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
        $this->assert->isInstanceOf($this->listener->results[1], IncompleteTestResult::class);
    }

    function runPublicMethods() {
        $suite = new RunStaticTestSuite_Bar(new TestFilter());
        $suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        $this->assert->equals($this->listener->started[1]->last(), "foo");
    }

    function filterMethods() {
        $suite = new RunStaticTestSuite_Foo((new TestFilter())
            ->filterMethod(function (\ReflectionMethod $method) {
                return strpos($method->getDocComment(), '@test');
            }));
        $suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert->equals($this->listener->started[1]->last(), "bar");
    }

    function runTestsInNewInstances() {
        $suite = new RunStaticTestSuite_Foo();
        RunStaticTestSuite_Foo::$constructed = 0;
        $suite->run($this->listener);

        $this->assert->equals(RunStaticTestSuite_Foo::$constructed, 2);
    }

    function callBeforeAndAfter() {
        $suite = new RunStaticTestSuite_BeforeAndAfter(new TestFilter());
        $suite->run($this->listener);

        $this->assert(RunStaticTestSuite_BeforeAndAfter::$calledBefore, 2);
        $this->assert(RunStaticTestSuite_BeforeAndAfter::$calledAfter, 2);
    }

    function discardParentName() {
        $suite = new RunStaticTestSuite_Empty(new TestFilter(), new TestName("Foo"));
        $suite->run($this->listener);

        $this->assert($this->listener->started[0]->toString(), RunStaticTestSuite_Empty::class);
    }
}

class RunStaticTestSuite_Empty extends StaticTestSuite {

}

class RunStaticTestSuite_Foo extends StaticTestSuite {

    public static $constructed = 0;

    function __construct(TestFilter $filter = null) {
        parent::__construct($filter ?: new TestFilter());
        self::$constructed++;
    }

    function __toString() {
        return "";
    }

    public function foo(Asserter $asserter) {
        $asserter->pass();
    }

    /**
     * @test
     */
    public function bar() {
        $this->markIncomplete("Not good");
    }

}

class RunStaticTestSuite_Bar extends StaticTestSuite {

    public function foo() {
    }

    protected function bar() {
        $this->baz();
    }

    private function baz() {
    }

    public static function staticFoo() {
    }

}

class RunStaticTestSuite_BeforeAndAfter extends StaticTestSuite {

    public static $calledAfter = 0;
    public static $calledBefore = 0;

    protected function before() {
        self::$calledBefore++;
    }

    protected function after() {
        self::$calledAfter++;
    }

    function foo() {
    }

    function bar() {
        $this->fail();
    }

}