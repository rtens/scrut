<?php
namespace watoki\scrut;

class Failure extends \RuntimeException {

    public function __construct($message = null) {
        parent::__construct($message);
    }

    public function getFailureFileAndLine() {
        $trace = $this->getTrace();
        return $trace[0]['file'] . ':' . $trace[0]['line'];
    }

    public function getFailureMessage() {
        return $this->getMessage();
    }
}