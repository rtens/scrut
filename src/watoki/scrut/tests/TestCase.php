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
use watoki\scrut\results\NotPassedTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\Test;
use watoki\scrut\TestRunListener;

abstract class TestCase extends Test {

    abstract protected function execute(Asserter $assert);

    /**
     * @param TestRunListener $listener
     * @return void
     */
    public function run(TestRunListener $listener) {
        $name = $this->getName();
        $listener->onStarted($name);

        $result = new PassedTestResult();
        $assert = new RecordingAsserter();

        try {
            $errorHandler = function ($code, $message, $file, $line) {
                if (error_reporting() == 0) return;
                throw new CaughtErrorFailure($message, $code, $file, $line);
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

        if ($result instanceof NotPassedTestResult) {
            $result->failure()->useSourceLocator($this->getFailureSourceLocator());
        }

        $listener->onResult($name, $result);
        $listener->onFinished($name);
    }

}