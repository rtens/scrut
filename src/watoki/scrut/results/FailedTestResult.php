<?php
namespace watoki\scrut\results;

use watoki\scrut\Failure;
use watoki\scrut\TestResult;

class FailedTestResult extends TestResult {

    private $failure;

    function __construct(Failure $failure) {
        $this->failure = $failure;
    }

    public function failure() {
        return $this->failure;
    }

}