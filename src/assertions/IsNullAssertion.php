<?php
namespace rtens\scrut\assertions;

class IsNullAssertion extends ValueAssertion {

    private $value;

    function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function describeFailure() {
        return $this->export($this->value) . ' should be NULL';
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return is_null($this->value);
    }
}