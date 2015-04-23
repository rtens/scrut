<?php
namespace watoki\scrut\suites;

use watoki\scrut\failures\EmptyTestSuiteFailure;
use watoki\scrut\results\IncompleteTestResult;
use watoki\scrut\TestRunListener;
use watoki\scrut\TestSuite;

class DynamicTestSuite extends TestSuite {

    /** @var string Name of suite */
    private $name;

    /** @var array|callable[] The tests indexed by their names */
    private $tests = [];

    /**
     * @param string $name Name of the suite
     * @param array|callable[] $tests The tests indexed by their names
     */
    function __construct($name, $tests = []) {
        $this->name = $name;
        $this->tests = $tests;
    }

    public function name() {
        return $this->name;
    }

    public function run(TestRunListener $listener) {
        if (!$this->tests) {
            $listener->onTestFinished($this->name(), new IncompleteTestResult(new EmptyTestSuiteFailure($this)));
        }

        foreach ($this->tests as $name => $callback) {
            $this->runTest($listener, $name, $callback);
        }
    }

    public function add($name, callable $callback) {
        $this->tests[$name] = $callback;
    }

}