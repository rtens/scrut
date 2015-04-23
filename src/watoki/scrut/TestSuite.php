<?php
namespace watoki\scrut;

use watoki\scrut\failures\CaughtExceptionFailure;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\PassedTestResult;

abstract class TestSuite {

    abstract public function run(ScrutinizeListener $listener);

    abstract protected function name();

    protected function runTest(ScrutinizeListener $listener, $testName, callable $test) {
        $name = $this->name() . '::' . $testName;

        $listener->onTestStarted($name);

        $caught = null;
        try {
            $test();
        } catch (\Exception $e) {
            $caught = $e;
        }

        if ($caught) {
            if (!($caught instanceof Failure)) {
                $caught = new CaughtExceptionFailure($caught);
            }
            $result = new FailedTestResult($caught);
        } else {
            $result = new PassedTestResult();
        }

        $listener->onTestFinished($name, $result);
    }
}