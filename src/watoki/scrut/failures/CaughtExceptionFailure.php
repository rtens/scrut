<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;

class CaughtExceptionFailure extends Failure {

    /** @var \Exception */
    private $exception;

    public function __construct(\Exception $exception) {
        parent::__construct($exception->getMessage());
        $this->exception = $exception;
    }

    public function getFailureMessage() {
        return "Caught [" . get_class($this->exception) . "]: " . $this->exception->getMessage();
    }

    public function getFailureFileAndLine() {
        return $this->exception->getFile() . ':' . $this->exception->getLine();
    }

    /**
     * @return \Exception
     */
    public function getException() {
        return $this->exception;
    }

}