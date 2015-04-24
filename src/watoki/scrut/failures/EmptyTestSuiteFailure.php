<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\GenericTestSuite;
use watoki\scrut\tests\StaticTestSuite;
use watoki\scrut\tests\TestSuite;

class EmptyTestSuiteFailure extends IncompleteTestFailure {

    /** @var TestSuite */
    private $testSuite;

    function __construct(TestSuite $testSuite) {
        parent::__construct();
        $this->testSuite = $testSuite;
    }

    public function getFailureMessage() {
        return "Empty test suite";
    }

    public function getLocation(TestSuite $suite) {
        if ($this->testSuite instanceof StaticTestSuite) {
            $class = new \ReflectionClass($this->testSuite);
            return $class->getFileName() . '(' . $class->getStartLine() . ')';
        } else if ($this->testSuite instanceof GenericTestSuite) {
            $creation = $this->testSuite->getCreation()->getTrace()[0];
            return $creation['file'] . '(' . $creation['line'] . ')';
        }
        return parent::getLocation($suite);
    }

}