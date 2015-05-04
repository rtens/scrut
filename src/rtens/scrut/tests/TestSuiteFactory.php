<?php
namespace rtens\scrut\tests;

use rtens\scrut\TestName;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\statics\StaticTestSuite;

class TestSuiteFactory {

    public function getTestSuite($class, TestFilter $filter, TestName $parent = null) {
        if (is_subclass_of($class, StaticTestSuite::class)) {
            return new $class($filter, $parent);
        }
        return new PlainTestSuite($filter, $class, $parent);
    }
}