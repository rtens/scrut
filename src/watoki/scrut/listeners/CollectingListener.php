<?php
namespace watoki\scrut\listeners;

use watoki\scrut\results\FailedTestResult;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\results\PassedTestResult;
use watoki\scrut\TestName;
use watoki\scrut\TestResult;
use watoki\scrut\TestRunListener;

class CollectingListener implements TestRunListener {

    /** @var array|array[]|TestResult[][] indexed by result class and test name */
    private $results = [];

    /** @var string[] The names of all started but not finished Tests */
    private $running = [];

    static protected $RESULT_CLASSES = [
        PassedTestResult::class,
        IncompleteTestResult::class,
        FailedTestResult::class
    ];

    /**
     * @return array|\watoki\scrut\TestResult[] indexed by test name
     */
    protected function getResults($resultClass) {
        return array_key_exists($resultClass, $this->results) ? $this->results[$resultClass]: [];
    }

    public function onStarted(TestName $test) {
        $this->running[] = $test->toString();
    }

    public function onResult(TestName $test, TestResult $result) {
        $this->results[get_class($result)][$test->toString()] = $result;
    }

    public function onFinished(TestName $test) {
        $key = array_search($test->toString(), $this->running);
        if ($key !== false) {
            unset($this->running[$key]);
        }

        if (!$this->running) {
            $this->onEnd();
        }
    }

    protected function onEnd() {
    }

}