<?php
namespace rtens\scrut\listeners;

use rtens\scrut\results\FailedTestResult;
use rtens\scrut\TestName;
use rtens\scrut\TestResult;
use rtens\scrut\TestRunListener;

class ResultListener implements TestRunListener {

    private $failed = false;

    public function hasFailed() {
        return $this->failed;
    }

    public function onResult(TestName $test, TestResult $result) {
        $this->failed = $this->failed || $result instanceof FailedTestResult;
    }

    public function onStarted(TestName $test) {
    }

    public function onFinished(TestName $test) {
    }
}