<?php
namespace watoki\scrut\tests;

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

        $tests = $this->getTests();
        if (!$tests) {
            $listener->onResult(new IncompleteTestResult(new EmptyTestSuiteFailure($this)));
        }

        foreach ($tests as $test) {
            $test->run($listener);
        }

        $listener->onFinished($this);
    }

    /**
     * @return Test[]
     */
    abstract protected function getTests();
}