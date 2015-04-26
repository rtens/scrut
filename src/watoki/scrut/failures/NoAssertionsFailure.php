<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\GenericTestCase;
use watoki\scrut\tests\PlainTestCase;
use watoki\scrut\tests\TestCase;

class NoAssertionsFailure extends IncompleteTestFailure {

    function __construct(TestCase $testCase) {
        parent::__construct("No assertions made", $this->determineLocation($testCase));
    }

    public function determineLocation($testCase) {
        if ($testCase instanceof PlainTestCase) {
            $method = $testCase->getMethod();
            return $this->formatFileAndLine($method->getFileName(), $method->getStartLine());
        } else if ($testCase instanceof GenericTestCase) {
            $creation = $testCase->getCreation()->getTrace()[0];
            return $this->formatFileAndLine($creation['file'], $creation['line']);
        }
        return 'unknown location';
    }
}