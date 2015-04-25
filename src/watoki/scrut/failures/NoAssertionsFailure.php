<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\GenericTestCase;
use watoki\scrut\tests\PlainTestCase;
use watoki\scrut\tests\TestCase;

class NoAssertionsFailure extends IncompleteTestFailure {

    /** @var TestCase */
    private $testCase;

    function __construct(TestCase $testCase) {
        parent::__construct();
        $this->testCase = $testCase;
    }

    public function getFailureMessage() {
        return "No assertions made";
    }

    public function getLocation() {
        if ($this->testCase instanceof PlainTestCase) {
            $method = $this->testCase->getMethod();
            return $this->formatFileAndLine($method->getFileName(), $method->getStartLine());
        } else if ($this->testCase instanceof GenericTestCase) {
            $creation = $this->testCase->getCreation()->getTrace()[0];
            return $this->formatFileAndLine($creation['file'], $creation['line']);
        }
        return 'unknown location';
    }
}