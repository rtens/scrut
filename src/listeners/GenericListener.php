<?php
namespace rtens\scrut\listeners;

use rtens\scrut\TestName;
use rtens\scrut\TestResult;
use rtens\scrut\TestRunListener;

class GenericListener implements TestRunListener {

    private $whenStarted;
    private $whenResult;
    private $whenFinished;

    function __construct() {
        $nothing = function () {
        };

        $this->whenStarted = $nothing;
        $this->whenFinished = $nothing;
        $this->whenResult = $nothing;
    }

    public function whenStarted(callable $callback) {
        $this->whenStarted = $callback;
        return $this;
    }

    public function whenFinished(callable $callback) {
        $this->whenFinished = $callback;
        return $this;
    }

    public function whenResult(callable $callback) {
        $this->whenResult = $callback;
        return $this;
    }

    public function onStarted(TestName $test) {
        call_user_func($this->whenStarted, $test);
    }

    public function onResult(TestName $test, TestResult $result) {
        call_user_func($this->whenResult, $test, $result);
    }

    public function onFinished(TestName $test) {
        call_user_func($this->whenFinished, $test);
    }
}