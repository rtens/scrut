<?php
namespace watoki\scrut\results;

use watoki\scrut\Failure;
use watoki\scrut\TestResult;

class NotPassedTestResult implements TestResult {

    private $failure;

    function __construct(Failure $failure) {
        $this->failure = $failure;
    }

    public function getFailure() {
        return $this->failure;
    }

}