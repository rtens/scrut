<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;

class CaughtExceptionFailure extends Failure {

    private $exception;

    public function __construct(\Exception $exception) {
        $location = $exception->getFile() . '(' . $exception->getLine() . ')';
        $failureMessage = "Caught [" . get_class($exception) . "] thrown at [" . $location . "]";
        $this->exception = $exception;

        parent::__construct($failureMessage, $exception->getMessage());
    }

    public function getException() {
        return $this->exception;
    }
}