<?php
namespace watoki\scrut\tests;

use watoki\scrut\failures\EmptyTestSuiteFailure;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\Test;
use watoki\scrut\TestRunListener;

abstract class TestSuite implements Test {

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

//    abstract public function name();
//
//    protected function runTest(TestRunListener $listener, $testName, callable $test) {
//        $name = $this->name() . '::' . $testName;
//
//        $listener->onTestStarted($name);
//
//        $result = new PassedTestResult();
//        try {
//            $asserter = new AsserterProxy(new Asserter());
//
//            $test($asserter);
//
//            if (!$asserter->hasAssertions()) {
//                throw new NoAssertionsFailure($this, $name);
//            }
//        } catch (IncompleteTestFailure $itf) {
//            $result = new IncompleteTestResult($itf);
//        } catch (Failure $f) {
//            $result = new FailedTestResult($f);
//        } catch (\Exception $e) {
//            $result = new FailedTestResult(new CaughtExceptionFailure($e));
//        }
//
//        $listener->onTestFinished($name, $result);
//    }
}