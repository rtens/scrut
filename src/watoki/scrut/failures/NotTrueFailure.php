<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;

class NotTrueFailure extends Failure {

    private $value;

    public function __construct($value, $message = null) {
        parent::__construct($message);
        $this->value = $value;
    }

    public function getFailureMessage() {
        return "[" . var_export($this->value, true) . "] is not true.";
    }
}