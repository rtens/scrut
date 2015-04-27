<?php
namespace watoki\scrut\listeners;

use watoki\scrut\Test;
use watoki\scrut\TestResult;
use watoki\scrut\TestRunListener;

class MultiListener implements TestRunListener {

    /** @var array|TestRunListener[] */
    private $listeners = [];

    public function add(TestRunListener $listener) {
        $this->listeners[] = $listener;
        return $this;
    }

    public function onStarted(Test $test) {
        foreach ($this->listeners as $listener) {
            $listener->onStarted($test);
        }
    }

    public function onFinished(Test $test) {
        foreach ($this->listeners as $listener) {
            $listener->onFinished($test);
        }
    }

    public function onResult(Test $test, TestResult $result) {
        foreach ($this->listeners as $listener) {
            $listener->onResult($test, $result);
        }
    }
}