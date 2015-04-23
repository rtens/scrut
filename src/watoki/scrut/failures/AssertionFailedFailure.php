<?php
namespace watoki\scrut\failures;

use watoki\scrut\Assertion;
use watoki\scrut\Failure;

class AssertionFailedFailure extends Failure {

    /** @var Assertion */
    private $assertion;

    public function __construct(Assertion $assertion, $message = null) {
        parent::__construct($message);
        $this->assertion = $assertion;
    }

    /**
     * @return Assertion
     */
    public function getAssertion() {
        return $this->assertion;
    }

    public function getFailureMessage() {
        return $this->assertion->describeFailure();
    }

}