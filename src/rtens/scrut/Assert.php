<?php
namespace rtens\scrut;

use rtens\scrut\assertions\ContainsAssertion;
use rtens\scrut\assertions\IsEqualAssertion;
use rtens\scrut\assertions\IsInstanceOfAssertion;
use rtens\scrut\assertions\IsNullAssertion;
use rtens\scrut\assertions\IsTrueAssertion;
use rtens\scrut\assertions\AssertOpposite;
use rtens\scrut\assertions\SizeAssertion;
use rtens\scrut\failures\AssertionFailedFailure;
use rtens\scrut\failures\IncompleteTestFailure;

class Assert {

    /**
     * @param Assertion $assertion
     * @param string $message
     */
    public function that(Assertion $assertion, $message = "") {
        if (!$assertion->checksOut()) {
            throw new AssertionFailedFailure($assertion, $message);
        }
    }

    public function fail($message = "") {
        throw new Failure("Failed", $message);
    }

    public function pass() {
        $this->that(new IsTrueAssertion(true));
    }

    public function incomplete($message = "") {
        throw new IncompleteTestFailure($message);
    }

    public function not($value = null, $equals = true) {
        if (!is_null($value)) {
            $this->not()->equals($value, $equals);
        }
        return new AssertOpposite($this);
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
        $this->that(new IsTrueAssertion($value), $message);
    }

    /**
     * @param mixed $value
     * @param mixed $expected
     * @param string $message
     */
    public function equals($value, $expected, $message = "") {
        $this->that(new IsEqualAssertion($value, $expected), $message);
    }

    /**
     * @param object $object
     * @param string $class
     * @param string $message
     */
    public function isInstanceOf($object, $class, $message = "") {
        $this->that(new IsInstanceOfAssertion($object, $class), $message);
    }

    /**
     * @param mixed $countable
     * @param int $size
     * @param string $message
     */
    public function size($countable, $size, $message = "") {
        $this->that(new SizeAssertion($countable, $size), $message);
    }

    /**
     * @param mixed[]|string $haystack
     * @param mixed $needle
     * @param string $message
     */
    public function contains($haystack, $needle, $message = "") {
        $this->that(new ContainsAssertion($haystack, $needle), $message);
    }

    public function isNull($value, $message = "") {
        $this->that(new IsNullAssertion($value), $message);
    }
}