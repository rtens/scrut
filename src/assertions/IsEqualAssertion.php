<?php
namespace rtens\scrut\assertions;

class IsEqualAssertion extends ValueAssertion {

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
        return $this->export($this->value) . ' should equal ' . $this->export($this->expected);
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return $this->value == $this->expected;
    }
}