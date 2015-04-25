<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\GenericTestSuite;
use watoki\scrut\tests\PlainTestSuite;
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

    public function getLocation() {
        if ($this->testSuite instanceof PlainTestSuite) {
            $class = new \ReflectionClass($this->testSuite->getSuite());
            return $this->formatFileAndLine($class->getFileName(), $class->getStartLine());
        } else if ($this->testSuite instanceof GenericTestSuite) {
            $creation = $this->testSuite->getCreation()->getTrace()[0];
            return $this->formatFileAndLine($creation['file'], $creation['line']);
        }
        return 'unknown location';
    }

}