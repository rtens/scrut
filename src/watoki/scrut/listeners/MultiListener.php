<?php
namespace watoki\scrut\listeners;

use watoki\scrut\TestRunListener;
use watoki\scrut\TestResult;

class MultiListener implements TestRunListener {

    /** @var array|TestRunListener[] */
    private $listeners = [];

    public function add(TestRunListener $listener) {
        $this->listeners[] = $listener;
    }

    public function onTestStarted($name) {
        foreach ($this->listeners as $listener) {
            $listener->onTestStarted($name);
        }
    }

    public function onTestFinished($name, TestResult $result) {
        foreach ($this->listeners as $listener) {
            $listener->onTestFinished($name, $result);
        }
    }

    public function onRunStarted() {
        foreach ($this->listeners as $listener) {
            $listener->onRunStarted();
        }
    }

    public function onRunFinished() {
        foreach ($this->listeners as $listener) {
            $listener->onRunFinished();
        }
    }
}