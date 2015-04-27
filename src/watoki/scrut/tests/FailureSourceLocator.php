<?php
namespace watoki\scrut\tests;

use watoki\scrut\Failure;
use watoki\scrut\failures\CaughtExceptionFailure;
use watoki\scrut\failures\EmptyTestSuiteFailure;
use watoki\scrut\failures\NoAssertionsFailure;

abstract class FailureSourceLocator {

    /**
     * @param array $trace
     * @return string
     */
    abstract protected function getExceptionSourceFromTrace($trace);

    /**
     * @return string
     */
    abstract protected function getEmptyTestSuiteFailureSource();

    /**
     * @return string
     */
    abstract protected function getNoAssertionsFailureSource();

    protected function unknownSource() {
        return "unknown source";
    }

    /**
     * @param Failure $failure
     * @return string
     */
    public function locate(Failure $failure) {
        if ($failure instanceof EmptyTestSuiteFailure) {
            return $this->getEmptyTestSuiteFailureSource($failure);
        } else if ($failure instanceof NoAssertionsFailure) {
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
        if (!isset($step['file']) || !isset($step['line'])) {
            return $this->unknownSource();
        }
        return self::formatFileAndLine($step['file'], $step['line']);
    }

    /**
     * @param string $file
     * @param int $line
     * @return string
     */
    public static function formatFileAndLine($file, $line) {
        return $file . ':' . $line;
    }
}