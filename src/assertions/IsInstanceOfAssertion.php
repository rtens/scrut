<?php
namespace rtens\scrut\assertions;

class IsInstanceOfAssertion extends ValueAssertion {

    private $object;
    private $class;

    function __construct($object, $class) {
        $this->object = $object;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function describeFailure() {
        if (!is_object($this->object)) {
            return $this->export($this->object) . " should be an object";
        } else {
            return $this->export($this->object) . " should be a <{$this->class}>";
        }
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return is_object($this->object) && is_a($this->object, $this->class);
    }
}