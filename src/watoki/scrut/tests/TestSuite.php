<?php
namespace watoki\scrut\tests;

use watoki\scrut\Failure;
use watoki\scrut\failures\EmptyTestSuiteFailure;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\Test;
use watoki\scrut\TestRunListener;

abstract class TestSuite implements Test {

    /**
     * @param TestRunListener $listener
     */
    public function run(TestRunListener $listener) {
        $listener->onStarted($this);

        $hasTest = false;
        foreach ($this->getTests() as $test) {
            $test->run($listener);
            $hasTest = true;
        }

        if (!$hasTest) {
            $listener->onResult($this, new IncompleteTestResult(new EmptyTestSuiteFailure($this)));
        }

        $listener->onFinished($this);
    }

    public function getFailureSource(Failure $failure) {
        if ($failure instanceof EmptyTestSuiteFailure) {
            return $this->getEmptyTestSuiteFailureSource($failure);
        }
        return "";
    }

    /**
     * @return Test[]
     */
    abstract protected function getTests();

    abstract protected function getEmptyTestSuiteFailureSource();
}