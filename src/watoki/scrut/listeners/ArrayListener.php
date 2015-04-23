<?php
namespace watoki\scrut\listeners;

use watoki\scrut\ScrutinizeListener;
use watoki\scrut\TestResult;

class ArrayListener implements ScrutinizeListener {

    /** @var array|string[] Names of started tests */
    private $started = [];

    /** @var array|TestResult[] Results indexed by name */
    private $results = [];

    public function started($name) {
        $this->started[] = $name;
    }

    public function finished($name, TestResult $result) {
        $this->results[$name] = $result;
    }

    /**
     * @param int|string $indexOrName
     * @return TestResult
     */
    public function getResult($indexOrName) {
        if (is_numeric($indexOrName)) {
            $indexOrName = $this->started[$indexOrName];
        }
        return $this->results[$indexOrName];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasStarted($name) {
        return in_array($name, $this->started);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasFinished($name) {
        return array_key_exists($name, $this->results);
    }

    /**
     * @return int Number of finished tests
     */
    public function count() {
        return count($this->results);
    }
}