<?php
namespace rtens\scrut\listeners;

use rtens\scrut\TestName;
use rtens\scrut\TestResult;
use rtens\scrut\TestRunListener;

class ArrayListener implements TestRunListener {

    /** @var array|TestName[] */
    public $started = [];

    /** @var array|TestName[] */
    public $finished = [];

    /** @var array|TestResult[] */
    public $results = [];

    public function onStarted(TestName $test) {
        $this->started[] = $test;
    }

    public function onFinished(TestName $test) {
        $this->finished[] = $test;
    }

    public function onResult(TestName $test, TestResult $result) {
        $this->results[] = $result;
    }
}