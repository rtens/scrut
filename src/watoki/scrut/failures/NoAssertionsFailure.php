<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\TestCase;

class NoAssertionsFailure extends IncompleteTestFailure {

    function __construct(TestCase $testCase) {
        parent::__construct("No assertions made");
    }
}