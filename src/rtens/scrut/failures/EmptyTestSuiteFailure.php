<?php
namespace rtens\scrut\failures;

use rtens\scrut\tests\FailureSourceLocator;
use rtens\scrut\tests\TestSuite;

class EmptyTestSuiteFailure extends IncompleteTestFailure {

    function __construct(TestSuite $testSuite) {
        parent::__construct("Empty test suite");
    }

    protected function getFailureSourceFromLocator(FailureSourceLocator $locator) {
        return $locator->locateEmptyTestFailureSource();
    }
}