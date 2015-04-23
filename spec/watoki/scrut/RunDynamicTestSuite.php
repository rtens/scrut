<?php
namespace spec\watoki\scrut;

use watoki\scrut\Asserter;
use watoki\scrut\failures\CaughtExceptionFailure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\listeners\ArrayListener;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\suites\DynamicTestSuite;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\Scrutinizer;
use watoki\scrut\suites\StaticTestSuite;

class RunDynamicTestSuite extends StaticTestSuite {

    /** @var ArrayListener */
    private $listener;

    /** @var Scrutinizer */
    private $scrutinizer;

    protected function before() {
        $this->listener = new ArrayListener();
        $this->scrutinizer = new Scrutinizer();
        $this->scrutinizer->listen($this->listener);
    }

    public function noSuites() {
        $this->scrutinizer->run();
        $this->assert->equals($this->listener->count(), 0);
    }

    public function emptySuite() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo"));
        $this->scrutinizer->run();

        $this->assert->equals($this->listener->count(), 1);

        /** @var IncompleteTestResult $testResult */
        $testResult = $this->listener->getResult(0);
        $this->assert($testResult instanceof IncompleteTestResult);
        $this->assert->equals($testResult->failure()->getFailureMessage(), "No tests found in [Foo]");
    }

    public function emptyTest() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
            }
        ]));
        $this->scrutinizer->run();

        $this->assert->equals($this->listener->count(), 1);
        $this->assert($this->listener->hasStarted("Foo::bar"));
        $this->assert($this->listener->hasFinished("Foo::bar"));
        $this->assert($this->listener->getResult(0) instanceof IncompleteTestResult);
    }

    public function secondListener() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
            }
        ]));
        $secondListener = new ArrayListener();
        $this->scrutinizer->listen($secondListener);
        $this->scrutinizer->run();

        $this->assert->equals($this->listener->count(), 1);
        $this->assert($this->listener->hasStarted("Foo::bar"));
        $this->assert($this->listener->hasFinished("Foo::bar"));
    }

    public function passingTest() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function (Asserter $assert) {
                $assert(true);
            }
        ]));
        $this->scrutinizer->run();

        $this->assert($this->listener->getResult(0) instanceof PassedTestResult);
        $this->assert($this->listener->getResult("Foo::bar") instanceof PassedTestResult);
    }

    public function failingTest() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
                throw new \Exception('Failed miserably');
            }
        ]));
        $this->scrutinizer->run();

        /** @var FailedTestResult $result */
        $result = $this->listener->getResult("Foo::bar");
        $this->assert($result instanceof FailedTestResult);
        $this->assert($result->failure() instanceof CaughtExceptionFailure);
        $this->assert->equals($result->failure()->getMessage(), "Failed miserably");
    }

    public function incompleteTest() {
        $this->scrutinizer->add(new DynamicTestSuite("Foo", [
            'bar' => function () {
                throw new IncompleteTestFailure('Not done yet');
            }
        ]));
        $this->scrutinizer->run();

        /** @var IncompleteTestResult $result */
        $result = $this->listener->getResult("Foo::bar");
        $this->assert($result instanceof IncompleteTestResult);
        $this->assert->equals($result->failure()->getFailureMessage(), "Not done yet");
    }
}