<?php
namespace watoki\scrut\assertions;

class IsTrueAssertion extends ValueAssertion {

    private $value;

    function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function describeFailure() {
        return $this->export($this->value) . " should be TRUE";
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return $this->value === true;
    }
}