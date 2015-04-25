<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;
use watoki\scrut\Failure;
use watoki\scrut\failures\CaughtErrorFailure;
use watoki\scrut\failures\CaughtExceptionFailure;
use watoki\scrut\failures\IncompleteTestFailure;
use watoki\scrut\failures\NoAssertionsFailure;
use watoki\scrut\RecordingAsserter;
use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\Test;
use watoki\scrut\TestRunListener;

abstract class TestCase implements Test {

    abstract protected function execute(Asserter $assert);

    /**
     * @param TestRunListener $listener
     * @return void
     */
    public function run(TestRunListener $listener) {
        $listener->onStarted($this);

        $result = new PassedTestResult();
        $assert = new RecordingAsserter();

        try {
            $errorHandler = function ($code, $message) {
                if (error_reporting() == 0) return;
                throw new CaughtErrorFailure($message, $code);
            };

            set_error_handler($errorHandler, E_ALL);
            $this->execute($assert);
            restore_error_handler();

            if (!$assert->hasMadeAssertions()) {
                $result = new IncompleteTestResult(new NoAssertionsFailure($this));
            }
        } catch (IncompleteTestFailure $it) {
            $result = new IncompleteTestResult($it);
        } catch (Failure $f) {
            $result = new FailedTestResult($f);
        } catch (\Exception $e) {
            $result = new FailedTestResult(new CaughtExceptionFailure($e));
        }

        $listener->onResult($result);
        $listener->onFinished($this);
    }
}