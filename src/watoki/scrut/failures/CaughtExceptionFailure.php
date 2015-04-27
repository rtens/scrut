<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;

class CaughtExceptionFailure extends Failure {

    private $exception;

    public function __construct(\Exception $exception) {
        $location = $exception->getFile() . '(' . $exception->getLine() . ')';
        $failureMessage = "Caught [" . get_class($exception) . "] thrown at [" . $location . "]";

        parent::__construct($failureMessage, $exception->getMessage());
        $this->exception = $exception;
    }

    public function getException() {
        return $this->exception;
    }
}