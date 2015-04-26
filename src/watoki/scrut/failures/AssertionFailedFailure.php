<?php
namespace watoki\scrut\failures;

use watoki\scrut\Assertion;
use watoki\scrut\Failure;

class AssertionFailedFailure extends Failure {

    public function __construct(Assertion $assertion, $userMessage = null) {
        parent::__construct($assertion->describeFailure(), $userMessage);
    }

}