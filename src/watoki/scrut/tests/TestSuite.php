<?php
namespace watoki\scrut\tests;

use watoki\scrut\failures\EmptyTestSuiteFailure;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\Test;
use watoki\scrut\TestRunListener;

abstract class TestSuite extends Test {

    /**
     * @return Test[]
     */
    abstract protected function getTests();

    /**
     * @param TestRunListener $listener
     */
    public function run(TestRunListener $listener) {
        $name = $this->getName();
        $listener->onStarted($name);

        $hasTest = false;
        foreach ($this->getTests() as $test) {
            $test->run($listener);
            $hasTest = true;
        }

        if (!$hasTest) {
            $listener->onResult($name, new IncompleteTestResult(
                (new EmptyTestSuiteFailure($this))
                    ->useSourceLocator($this->getFailureSourceLocator())
            ));
        }

        $listener->onFinished($name);
    }
}