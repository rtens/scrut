<?php
namespace spec\watoki\scrut;

use watoki\scrut\Asserter;
use watoki\scrut\failures\AssertionFailedFailure;
use watoki\scrut\failures\CaughtExceptionFailure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\tests\GenericTestCase;
use watoki\scrut\tests\GenericTestSuite;
use watoki\scrut\tests\StaticTestSuite;

class RunDynamicTestSuite extends StaticTestSuite {

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

        $this->assert->count($this->listener->results, 1);

        /** @var IncompleteTestResult $testResult */
        $testResult = $this->listener->results[0];
        $this->assert->isInstanceOf($testResult, IncompleteTestResult::class);
        $this->assert->equals($testResult->failure()->getFailureMessage(), "Empty test suite");
    }

    function emptyTest() {
        $this->suite->add(new GenericTestCase("bar", function () {
        }));
        $this->suite->run($this->listener);

        $this->assert->count($this->listener->started, 2);
        $this->assert->equals($this->listener->started[0]->getName(), "Foo");
        $this->assert->equals($this->listener->started[1]->getName(), "bar");

        $this->assert->count($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], IncompleteTestResult::class);
    }

    function passingTest() {
        $this->suite->test("bar", function (Asserter $assert) {
            $assert(true);
        });
        $this->suite->run($this->listener);

        $this->assert->count($this->listener->results, 1);
        $this->assert->isInstanceOf($this->listener->results[0], PassedTestResult::class);
    }

    function failingTest() {
        $this->suite->test("bar", function (Asserter $assert) {
            $assert(false);
        });
        $this->suite->run($this->listener);

        $this->assert->count($this->listener->results, 1);
        /** @var FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, FailedTestResult::class);
        $this->assert->isInstanceOf($result->failure(), AssertionFailedFailure::class);
    }

    function exceptionInTest() {
        $this->suite->test("bar", function () {
            throw new \InvalidArgumentException('Failed miserably');
        });
        $this->suite->run($this->listener);

        $this->assert->count($this->listener->results, 1);
        /** @var FailedTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, FailedTestResult::class);
        $this->assert->isInstanceOf($result->failure(), CaughtExceptionFailure::class);
        $this->assert->contains($result->failure()->getFailureMessage(), "Caught [InvalidArgumentException] thrown at [" . __FILE__);
        $this->assert->equals($result->failure()->getMessage(), "Failed miserably");
    }

    function incompleteTest() {
        $this->suite->test("bar", function () {
            throw new IncompleteTestFailure('Not done yet');
        });
        $this->suite->run($this->listener);

        $this->assert->count($this->listener->results, 1);
        /** @var IncompleteTestResult $result */
        $result = $this->listener->results[0];
        $this->assert->isInstanceOf($result, IncompleteTestResult::class);
        $this->assert->equals($result->failure()->getFailureMessage(), "");
        $this->assert->equals($result->failure()->getMessage(), "Not done yet");
    }
}