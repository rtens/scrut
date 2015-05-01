<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\FailureSourceLocator;
use watoki\scrut\tests\TestCase;

class NoAssertionsFailure extends IncompleteTestFailure {

    function __construct(TestCase $testCase) {
        parent::__construct("No assertions made");
    }

    protected function getFailureSourceFromLocator(FailureSourceLocator $locator) {
        return $locator->locateEmptyTestFailureSource();
    }
}