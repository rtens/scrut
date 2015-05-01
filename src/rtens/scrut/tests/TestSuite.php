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