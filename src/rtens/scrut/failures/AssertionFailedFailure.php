<?php
namespace rtens\scrut\failures;

use rtens\scrut\Assertion;
use rtens\scrut\Failure;

class AssertionFailedFailure extends Failure {

    public function __construct(Assertion $assertion, $userMessage = null) {
        parent::__construct($assertion->describeFailure(), $userMessage);
    }

}