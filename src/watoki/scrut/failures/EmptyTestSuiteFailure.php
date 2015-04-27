<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\TestSuite;

class EmptyTestSuiteFailure extends IncompleteTestFailure {

    function __construct(TestSuite $testSuite) {
        parent::__construct("Empty test suite");
    }

}