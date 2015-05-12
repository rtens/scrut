<?php
namespace rtens\scrut\tests;

use rtens\scrut\failures\EmptyTestSuiteFailure;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\Test;
use rtens\scrut\TestRunListener;

abstract class TestSuite extends Test {

    /**
     * @return Test[]
     */
    abstract public function getTests();

    /**
     * @param TestRunListener $listener
     */
    public function run(TestRunListener $listener) {
        $name = $this->getName();
        $listener->onStarted($name);

        $hasTests = $this->runTests($listener);

        if (!$hasTests) {
            $listener->onResult($name, $this->createIncompleteTestResult());
        }

        $listener->onFinished($name);
    }

    private function runTests(TestRunListener $listener) {
        $hasTest = false;
        foreach ($this->getTests() as $test) {
            $test->run($listener);
            $hasTest = true;
        }
        return $hasTest;
    }

    private function createIncompleteTestResult() {
        $failure = new EmptyTestSuiteFailure($this);
        $failure->useSourceLocator($this->getFailureSourceLocator());

        return new IncompleteTestResult($failure);
    }
}