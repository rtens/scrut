<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;
use watoki\scrut\tests\TestSuite;

class CaughtExceptionFailure extends Failure {

    /** @var \Exception */
    private $exception;

    public function __construct(\Exception $exception) {
        parent::__construct($exception->getMessage());
        $this->exception = $exception;
    }

    public function getFailureMessage() {
        return "Caught [" . get_class($this->exception) . "] thrown at [{$this->exception->getFile()}({$this->exception->getLine()})]";
    }

    public function getLocation(TestSuite $suite) {
        return $this->findLocation($suite, $this->exception);
    }

    /**
     * @return \Exception
     */
    public function getException() {
        return $this->exception;
    }

}