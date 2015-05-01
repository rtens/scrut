<?php
namespace rtens\scrut\results;

use rtens\scrut\Failure;
use rtens\scrut\TestResult;

class NotPassedTestResult implements TestResult {

    private $failure;

    function __construct(Failure $failure) {
        $this->failure = $failure;
    }

    public function getFailure() {
        return $this->failure;
    }

}