<?php
namespace spec\watoki\scrut;

use watoki\scrut\failures\EmptyTestSuiteFailure;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\tests\StaticTestSuite;

class RunStaticTestSuite extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    protected function before() {
        $this->listener = new ArrayListener();
    }

    function emptySuite() {
        $suite = new RunStaticTestSuite_Empty();
        $suite->run($this->listener);

        $this->assert->count($this->listener->results, 1);
        /** @var IncompleteTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, IncompleteTestResult::class);
        $this->assert->isInstanceOf($result->failure(), EmptyTestSuiteFailure::class);
    }

    function runOwnMethods() {
        $suite = new RunStaticTestSuite_Foo();
        $suite->run($this->listener);

        $this->assert->count($this->listener->started, 3);
        $this->assert->equals($this->listener->started[0]->getName(), RunStaticTestSuite_Foo::class);
        $this->assert->equals($this->listener->started[1]->getName(), "foo");
        $this->assert->equals($this->listener->started[2]->getName(), "bar");

        $this->assert->count($this->listener->results, 2);
        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
        $this->assert->isInstanceOf($this->listener->results[1], IncompleteTestResult::class);
    }

    function runPublicMethods() {
        $suite = new RunStaticTestSuite_Bar();
        $suite->run($this->listener);

        $this->assert->count($this->listener->results, 1);
        $this->assert->equals($this->listener->started[1]->getName(), "foo");
    }

    function filterMethods() {
    }

    function runTestsInNewInstances() {
    }
}

class RunStaticTestSuite_Empty extends StaticTestSuite {

}

class RunStaticTestSuite_Foo extends StaticTestSuite {

    public function foo() {
        $this->assert(true);
    }

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