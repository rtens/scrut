<?php
namespace watoki\scrut;

class FailedTestResult extends TestResult {

    private $exception;

    function __construct(\Exception $exception) {
        $this->exception = $exception;
    }

    public function exception() {
        return $this->exception;
    }

}