<?php
namespace watoki\scrut\assertions;

use watoki\scrut\Assertion;

class IsTrueAssertion implements Assertion {

    private $value;

    function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function describeFailure() {
        return "[" . var_export($this->value, true) . "] should be true";
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return $this->value === true;
    }
}