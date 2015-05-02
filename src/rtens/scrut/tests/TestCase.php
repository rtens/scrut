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

        $result = new PassedTestResult();
        $assert = new RecordingAssert();

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
            $result->getFailure()->useSourceLocator($this->getFailureSourceLocator());
        }

        $listener->onResult($name, $result);
        $listener->onFinished($name);
    }

}