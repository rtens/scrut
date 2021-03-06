<?php
namespace spec\rtens\scrut;

use rtens\scrut\Assert;
use rtens\scrut\failures\AssertionFailedFailure;
use rtens\scrut\failures\CaughtExceptionFailure;
use rtens\scrut\failures\IncompleteTestFailure;
use rtens\scrut\listeners\ArrayListener;
use rtens\scrut\results\FailedTestResult;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\results\PassedTestResult;
use rtens\scrut\tests\generic\GenericTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;

class RunGenericTestSuite extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    /** @var GenericTestSuite */
    private $suite;

    protected function before() {
        $this->listener = new ArrayListener();
        $this->suite = new GenericTestSuite("Foo");
    }

    function emptySuite() {
        $this->suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);

        /** @var IncompleteTestResult $testResult */
        $testResult = $this->listener->results[0];
        $this->assert->isInstanceOf($testResult, IncompleteTestResult::class);
        $this->assert->equals($testResult->getFailure()->getFailureMessage(), "Empty test suite");
    }

    function emptyTest() {
        $this->suite->test("bar", function () {
        });
        $this->suite->run($this->listener);

        $this->assert->size($this->listener->started, 2);
        $this->assert->equals($this->listener->started[0]->toString(), "Foo");
        $this->assert->equals($this->listener->started[1]->toString(), "Foo::bar");

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function passingTest() {
        $this->suite->test("bar", function (Assert $assert) {
            $assert(true);
        });
        $this->suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function failingTest() {
        $this->suite->test("bar", function (Assert $assert) {
            $assert(false);
        });
        $this->suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, FailedTestResult::class);
        $this->assert->isInstanceOf($result->getFailure(), AssertionFailedFailure::class);
    }

    function exceptionInTest() {
        $this->suite->test("bar", function () {
            throw new \InvalidArgumentException('Failed miserably');
        });
        $this->suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, FailedTestResult::class);
        $this->assert->isInstanceOf($result->getFailure(), CaughtExceptionFailure::class);
        $this->assert->contains($result->getFailure()->getFailureMessage(), "Caught [InvalidArgumentException] thrown at [" . __FILE__);
        $this->assert->equals($result->getFailure()->getMessage(), "Failed miserably");
    }

    function incompleteTest() {
        $this->suite->test("bar", function () {
            throw new IncompleteTestFailure('Not done yet');
        });
        $this->suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        /** @var IncompleteTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, IncompleteTestResult::class);
        $this->assert->equals($result->getFailure()->getFailureMessage(), "Not done yet");
        $this->assert->equals($result->getFailure()->getMessage(), "");
    }

    function composedSuites() {
        $this->suite->suite("Bar", function (GenericTestSuite $suite) {
            $suite->test("baz", function () {
            });
        });
        $this->suite->run($this->listener);

        $this->assert->size($this->listener->results, 1);
        $this->assert->size($this->listener->started, 3);
        $this->assert($this->listener->started[0]->toString(), "Foo");
        $this->assert($this->listener->started[1]->toString(), "Foo::Bar");
        $this->assert($this->listener->started[2]->toString(), "Foo::Bar::baz");
    }
}