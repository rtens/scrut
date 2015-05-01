<?php
namespace rtens\scrut;

use rtens\scrut\assertions\ContainsAssertion;
use rtens\scrut\assertions\IsEqualAssertion;
use rtens\scrut\assertions\IsInstanceOfAssertion;
use rtens\scrut\assertions\IsNullAssertion;
use rtens\scrut\assertions\IsTrueAssertion;
use rtens\scrut\assertions\NotAsserter;
use rtens\scrut\assertions\SizeAssertion;
use rtens\scrut\failures\AssertionFailedFailure;
use rtens\scrut\failures\IncompleteTestFailure;

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

    public function fail($message = "") {
        throw new Failure("Failed", $message);
    }

    public function pass() {
        $this->assert(new IsTrueAssertion(true));
    }

    public function incomplete($message = "") {
        throw new IncompleteTestFailure($message);
    }

    public function not() {
        return new NotAsserter($this);
    }

    /**
     * @param mixed $value
     * @param bool|mixed $equals
     */
    function __invoke($value, $equals = true) {
        $this->equals($value, $equals);
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

    public function isNull($value, $message = "") {
        $this->assert(new IsNullAssertion($value), $message);
    }
}