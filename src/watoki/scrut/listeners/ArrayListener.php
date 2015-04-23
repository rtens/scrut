<?php
namespace watoki\scrut\listeners;

use watoki\scrut\Test;
use watoki\scrut\TestResult;
use watoki\scrut\TestRunListener;

class ArrayListener implements TestRunListener {

    /** @var array|Test[] */
    public $started = [];

    /** @var array|Test[] */
    public $finished = [];

    /** @var array|TestResult[] */
    public $results = [];

    public function onStarted(Test $test) {
        $this->started[] = $test;
    }

    public function onFinished(Test $test) {
        $this->finished[] = $test;
    }

    public function onResult(TestResult $result) {
        $this->results[] = $result;
    }
}