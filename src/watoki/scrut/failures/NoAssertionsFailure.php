<?php
namespace watoki\scrut\failures;

use watoki\scrut\TestSuite;

class NoAssertionsFailure extends EmptyTestSuiteFailure {

    private $testName;

    public function __construct(TestSuite $suite, $testName) {
        parent::__construct($suite);
        $this->testName = $testName;
    }

    public function getFailureMessage() {
        return "No assertions made in [{$this->testName}]";
    }
}