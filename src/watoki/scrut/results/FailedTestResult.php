<?php
namespace watoki\scrut\results;

use watoki\scrut\Failure;
use watoki\scrut\TestResult;

class FailedTestResult implements TestResult {

    private $failure;

    function __construct(Failure $failure) {
        $this->failure = $failure;
    }

    public function failure() {
        return $this->failure;
    }

}