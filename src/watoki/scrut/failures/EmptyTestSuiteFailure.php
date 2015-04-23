<?php
namespace watoki\scrut\failures;

use watoki\scrut\Failure;
use watoki\scrut\TestSuite;

class EmptyTestSuiteFailure extends Failure {

    /** @var TestSuite */
    private $suite;

    public function __construct(TestSuite $suite) {
        parent::__construct();
        $this->suite = $suite;
    }

    public function getFailureMessage() {
        return "Empty test suite";
    }

    public function getFailureFileAndLine() {
        $class = new \ReflectionClass($this->suite);
        return $class->getFileName() . ':' . $class->getStartLine();
    }

}