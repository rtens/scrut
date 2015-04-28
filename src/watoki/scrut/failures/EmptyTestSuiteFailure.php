<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\FailureSourceLocator;
use watoki\scrut\tests\TestSuite;

class EmptyTestSuiteFailure extends IncompleteTestFailure {

    function __construct(TestSuite $testSuite) {
        parent::__construct("Empty test suite");
    }

    protected function getFailureSourceFromLocator(FailureSourceLocator $locator) {
        return $locator->locateEmptyTestSuiteFailureSource();
    }
}