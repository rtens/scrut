<?php
namespace watoki\scrut\failures;

use watoki\scrut\TestSuite;

class EmptyTestSuiteFailure extends IncompleteTestFailure {

    /** @var TestSuite */
    private $suite;

    public function __construct(TestSuite $suite) {
        parent::__construct();
        $this->suite = $suite;
    }

    public function getFailureMessage() {
        return "No tests found in [{$this->suite->name()}]";
    }

    public function getFailureFileAndLine() {
        $class = new \ReflectionClass($this->suite);
        return $class->getFileName() . ':' . $class->getStartLine();
    }

}