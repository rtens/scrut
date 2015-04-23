<?php
namespace watoki\scrut;

use watoki\scrut\assertions\IsEqualAssertion;
use watoki\scrut\assertions\IsInstanceOfAssertion;
use watoki\scrut\assertions\IsTrueAssertion;
use watoki\scrut\failures\AssertionFailedFailure;

class Asserter {

    public function assert(Assertion $assertion, $message = "") {
        if (!$assertion->checksOut()) {
            throw new AssertionFailedFailure($assertion, $message);
        }
    }

    function __invoke($value, $message = "") {
        $this->isTrue($value, $message);
    }

    public function isTrue($value, $message = "") {
        $this->assert(new IsTrueAssertion($value), $message);
    }

    public function equals($value, $expected, $message = "") {
        $this->assert(new IsEqualAssertion($value, $expected), $message);
    }

    public function isInstanceOf($object, $class, $message = "") {
        $this->assert(new IsInstanceOfAssertion($object, $class), $message);
    }
}