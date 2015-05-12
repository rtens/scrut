<?php
namespace rtens\scrut\tests;

use rtens\scrut\Assert;
use rtens\scrut\Failure;
use rtens\scrut\failures\CaughtErrorFailure;
use rtens\scrut\failures\CaughtExceptionFailure;
use rtens\scrut\failures\IncompleteTestFailure;
use rtens\scrut\failures\NoAssertionsFailure;
use rtens\scrut\RecordingAssert;
use rtens\scrut\results\FailedTestResult;
use rtens\scrut\results\IncompleteTestResult;
use rtens\scrut\results\NotPassedTestResult;
use rtens\scrut\results\PassedTestResult;
use rtens\scrut\Test;
use rtens\scrut\TestRunListener;

abstract class TestCase extends Test {

    abstract protected function execute(Assert $assert);

    /**
     * @param TestRunListener $listener
     * @return void
     */
    public function run(TestRunListener $listener) {
        $name = $this->getName();
        $listener->onStarted($name);

        $result = $this->runTestCase();

        $listener->onResult($name, $result);
        $listener->onFinished($name);
    }

    private function runTestCase() {
        $assert = new RecordingAssert();

        try {
            $this->executeTestCase($assert);

            if (!$assert->hasMadeAssertions()) {
                return $this->injectLocator(new IncompleteTestResult(new NoAssertionsFailure($this)));
            }
        } catch (IncompleteTestFailure $it) {
            return $this->injectLocator(new IncompleteTestResult($it));
        } catch (Failure $f) {
            return $this->injectLocator(new FailedTestResult($f));
        } catch (\Exception $e) {
            return $this->injectLocator(new FailedTestResult(new CaughtExceptionFailure($e)));
        }

        return new PassedTestResult();
    }

    private function executeTestCase(Assert $assert) {
        set_error_handler([$this, 'handleError'], E_ALL);
        $this->execute($assert);
        restore_error_handler();
    }

    public function handleError($code, $message, $file, $line) {
        if (error_reporting() == 0) return;
        throw new CaughtErrorFailure($message, $code, $file, $line);
    }

    private function injectLocator(NotPassedTestResult $result) {
        $result->getFailure()->useSourceLocator($this->getFailureSourceLocator());
        return $result;
    }

}