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
                return new IncompleteTestResult($this->injectLocator(new NoAssertionsFailure($this)));
            }
        } catch (IncompleteTestFailure $it) {
            return new IncompleteTestResult($this->injectLocator($it));
        } catch (Failure $f) {
            return new FailedTestResult($this->injectLocator($f));
        } catch (\Exception $e) {
            return new FailedTestResult($this->injectLocator(new CaughtExceptionFailure($e)));
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

    /**
     * @param Failure $failure
     * @return Failure|IncompleteTestFailure
     */
    private function injectLocator(Failure $failure) {
        $failure->useSourceLocator($this->getFailureSourceLocator());
        return $failure;
    }

}