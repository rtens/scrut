<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;

class NotEqualFailure extends Failure {

    private $value;
    private $expected;

    public function __construct($value, $expected, $message = null) {
        parent::__construct($message);
        $this->value = $value;
        $this->expected = $expected;
    }

    public function getFailureMessage() {
        return 'Got [' . var_export($this->value, true) . ']'
        . ' but expected [' . var_export($this->expected, true) . ']'
        . (parent::getFailureMessage() ? (' (' . parent::getFailureMessage() . ')') : '');
    }
}