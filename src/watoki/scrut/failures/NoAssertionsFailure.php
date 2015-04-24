<?php
namespace watoki\scrut\failures;

use watoki\scrut\tests\GenericTestCase;
use watoki\scrut\tests\StaticTestCase;
use watoki\scrut\tests\TestCase;
use watoki\scrut\tests\TestSuite;

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

    public function getLocation(TestSuite $suite) {
        if ($this->testCase instanceof StaticTestCase) {
            $method = $this->testCase->getMethod();
            return $method->getFileName() . '(' . $method->getStartLine() . ')';
        } else if ($this->testCase instanceof GenericTestCase) {
            $creation = $this->testCase->getCreation()->getTrace()[0];
            return $creation['file'] . '(' . $creation['line'] . ')';
        }
        return parent::getLocation($suite);
    }
}