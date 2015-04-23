<?php
namespace watoki\scrut\listeners;

use watoki\scrut\TestRunListener;
use watoki\scrut\TestResult;

class CallbackListener implements TestRunListener {

    private $testStartedCallbacks = [];
    private $testFinishedCallbacks = [];
    private $runStartedCallbacks = [];
    private $runFinishedCallbacks = [];

    /**
     * @param callable $callback Will be called after a test run
     * @return $this
     */
    public function whenRunStarted(callable $callback) {
        $this->runStartedCallbacks[] = $callback;
        return $this;
    }

    /**
     * @param callable $callback Will be invoked at the beginning of a test run
     * @return $this
     */
    public function whenRunFinished(callable $callback) {
        $this->runFinishedCallbacks[] = $callback;
        return $this;
    }

    /**
     * @param callable $callback Will be called with name of test
     * @return $this
     */
    public function whenTestStarted(callable $callback) {
        $this->testStartedCallbacks[] = $callback;
        return $this;
    }

    /**
     * @param callable $callback Will be called with name of test and its TestResult
     * @return $this
     */
    public function whenTestFinished(callable $callback) {
        $this->testFinishedCallbacks[] = $callback;
        return $this;
    }

    public function onTestStarted($name) {
        foreach ($this->testStartedCallbacks as $callback) {
            $callback($name);
        }
    }

    public function onTestFinished($name, TestResult $result) {
        foreach ($this->testFinishedCallbacks as $callback) {
            $callback($name, $result);
        }
    }

    public function onRunStarted() {
        foreach ($this->runStartedCallbacks as $callback) {
            $callback();
        }
    }

    public function onRunFinished() {
        foreach ($this->runFinishedCallbacks as $callback) {
            $callback();
        }
    }
}