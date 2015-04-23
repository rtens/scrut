<?php
namespace watoki\scrut\listeners;

use watoki\scrut\ScrutinizeListener;
use watoki\scrut\TestResult;

class MultiListener implements ScrutinizeListener {

    /** @var array|ScrutinizeListener[] */
    private $listeners = [];

    public function add(ScrutinizeListener $listener) {
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