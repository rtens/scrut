<?php
namespace watoki\scrut;

use watoki\scrut\assertions\ContainsAssertion;
use watoki\scrut\assertions\IsEqualAssertion;
use watoki\scrut\assertions\IsInstanceOfAssertion;
use watoki\scrut\assertions\IsTrueAssertion;
use watoki\scrut\assertions\SizeAssertion;
use watoki\scrut\failures\AssertionFailedFailure;

class Asserter {

    public function assert(Assertion $assertion, $message = "") {
        if (!$assertion->checksOut()) {
            throw new AssertionFailedFailure($assertion, $message);
        }
    }

    function __invoke($value, $equals = true) {
        if ($equals === true) {
            $this->isTrue($value);
        } else {
            $this->equals($value, $equals);
        }
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

    public function size($countable, $size, $message = "") {
        $this->assert(new SizeAssertion($countable, $size), $message);
    }

    public function contains($haystack, $needle, $message = "") {
        $this->assert(new ContainsAssertion($haystack, $needle), $message);
    }
}