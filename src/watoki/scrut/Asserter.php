<?php
namespace watoki\scrut;

use watoki\scrut\assertions\ContainsAssertion;
use watoki\scrut\assertions\IsEqualAssertion;
use watoki\scrut\assertions\IsInstanceOfAssertion;
use watoki\scrut\assertions\IsTrueAssertion;
use watoki\scrut\assertions\SizeAssertion;
use watoki\scrut\failures\AssertionFailedFailure;

class Asserter {

    /**
     * @param Assertion $assertion
     * @param string $message
     */
    public function assert(Assertion $assertion, $message = "") {
        if (!$assertion->checksOut()) {
            throw new AssertionFailedFailure($assertion, $message);
        }
    }

    /**
     * @param mixed $value
     * @param bool|mixed $equals
     */
    function __invoke($value, $equals = true) {
        if ($equals === true) {
            $this->isTrue($value);
        } else {
            $this->equals($value, $equals);
        }
    }

    /**
     * @param boolean $value
     * @param string $message
     */
    public function isTrue($value, $message = "") {
        $this->assert(new IsTrueAssertion($value), $message);
    }

    /**
     * @param mixed $value
     * @param mixed $expected
     * @param string $message
     */
    public function equals($value, $expected, $message = "") {
        $this->assert(new IsEqualAssertion($value, $expected), $message);
    }

    /**
     * @param object $object
     * @param string $class
     * @param string $message
     */
    public function isInstanceOf($object, $class, $message = "") {
        $this->assert(new IsInstanceOfAssertion($object, $class), $message);
    }

    /**
     * @param mixed $countable
     * @param int $size
     * @param string $message
     */
    public function size($countable, $size, $message = "") {
        $this->assert(new SizeAssertion($countable, $size), $message);
    }

    /**
     * @param mixed[]|string $haystack
     * @param mixed $needle
     * @param string $message
     */
    public function contains($haystack, $needle, $message = "") {
        $this->assert(new ContainsAssertion($haystack, $needle), $message);
    }
}