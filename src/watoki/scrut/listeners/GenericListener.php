<?php
namespace watoki\scrut\listeners;

use watoki\scrut\Test;
use watoki\scrut\TestResult;
use watoki\scrut\TestRunListener;

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
    }

    public function whenFinished(callable $callback) {
        $this->whenFinished = $callback;
    }

    public function whenResult(callable $callback) {
        $this->whenResult = $callback;
    }

    public function onStarted(Test $test) {
        call_user_func($this->whenStarted, $test);
    }

    public function onResult(TestResult $result) {
        call_user_func($this->whenResult, $result);
    }

    public function onFinished(Test $test) {
        call_user_func($this->whenFinished, $test);
    }
}