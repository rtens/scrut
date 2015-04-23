<?php
namespace watoki\scrut;

use watoki\scrut\failures\CaughtExceptionFailure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\PassedTestResult;

abstract class TestSuite {

    abstract public function run(ScrutinizeListener $listener);

    abstract public function name();

    protected function runTest(ScrutinizeListener $listener, $testName, callable $test) {
        $name = $this->name() . '::' . $testName;

        $listener->onTestStarted($name);

        $result = new PassedTestResult();
        try {
            $asserter = new AsserterProxy(new Asserter());
            $test($asserter);
            if (!$asserter->hasCalls()) {
                throw new IncompleteTestFailure("No assertions made in [$name]");
            }
        } catch (IncompleteTestFailure $itf) {
            $result = new IncompleteTestResult($itf);
        } catch (Failure $f) {
            $result = new FailedTestResult($f);
        } catch (\Exception $e) {
            $result = new FailedTestResult(new CaughtExceptionFailure($e));
        }

        $listener->onTestFinished($name, $result);
    }
}