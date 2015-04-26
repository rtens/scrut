<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\GenericTestSuite;
use watoki\scrut\tests\PlainTestSuite;
use watoki\scrut\tests\TestSuite;

class EmptyTestSuiteFailure extends IncompleteTestFailure {

    function __construct(TestSuite $testSuite) {
        parent::__construct("Empty test suite", $this->determineLocation($testSuite));
    }

    public function determineLocation($testSuite) {
        if ($testSuite instanceof PlainTestSuite) {
            $class = new \ReflectionClass($testSuite->getSuite());
            return $this->formatFileAndLine($class->getFileName(), $class->getStartLine());
        } else if ($testSuite instanceof GenericTestSuite) {
            $creation = $testSuite->getCreation()->getTrace()[0];
            return $this->formatFileAndLine($creation['file'], $creation['line']);
        }
        return 'unknown location';
    }

}