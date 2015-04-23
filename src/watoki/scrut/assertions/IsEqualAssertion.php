<?php
namespace watoki\scrut\assertions;

use watoki\scrut\Assertion;

class IsEqualAssertion implements Assertion {

    private $value;
    private $expected;

    function __construct($value, $expected) {
        $this->value = $value;
        $this->expected = $expected;
    }

    /**
     * @return string
     */
    public function describeFailure() {
        return '[' . var_export($this->value, true) . ']'
        . ' should be [' . var_export($this->expected, true) . ']';
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return $this->value === $this->expected;
    }
}