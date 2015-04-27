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

        $listener->onResult($this, $result);
        $listener->onFinished($this);
    }

    public function getFailureSource(Failure $failure) {
        if ($failure instanceof NoAssertionsFailure) {
            return $this->getNoAssertionsFailureSource();
        } else if ($failure instanceof CaughtExceptionFailure) {
            return $this->getExceptionSource($failure->getException());
        } else {
            return $this->getExceptionSource($failure);
        }
    }

    private function getExceptionSource(\Exception $exception) {
        $first = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'class' => null
        ];

        return $this->getExceptionSourceFromTrace(array_merge([$first], $exception->getTrace()));
    }

    protected function formatStep($step) {
        return $this->formatFileAndLine($step['file'], $step['line']);
    }

    /**
     * @param string $file
     * @param int $line
     * @return string
     */
    protected function formatFileAndLine($file, $line) {
        return $file . ':' . $line;
    }

    abstract protected function getNoAssertionsFailureSource();

    abstract protected function getExceptionSourceFromTrace($trace);

}