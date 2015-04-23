<?php
namespace watoki\scrut;

class Failure extends \RuntimeException {

    public function __construct($message = null) {
        parent::__construct($message);
    }

    public function getFailureFileAndLine() {
        foreach ($this->getTrace() as $step) {
            if (strpos($step['file'], 'src\\watoki\\scrut') === false) {
                return $step['file'] . ':' . $step['line'];
            }
        }
        return 'unknown source';
    }

    public function getFailureMessage() {
        return $this->getMessage();
    }
}