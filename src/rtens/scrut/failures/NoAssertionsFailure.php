<?php
namespace rtens\scrut\failures;

use rtens\scrut\tests\FailureSourceLocator;
use rtens\scrut\tests\TestCase;

class NoAssertionsFailure extends IncompleteTestFailure {

    function __construct(TestCase $testCase) {
        parent::__construct("No assertions made");
    }

    protected function getFailureSourceFromLocator(FailureSourceLocator $locator) {
        return $locator->locateEmptyTestFailureSource();
    }
}