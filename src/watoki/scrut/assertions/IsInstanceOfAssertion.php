<?php
namespace watoki\scrut\assertions;

use watoki\scrut\Assertion;

class IsInstanceOfAssertion implements Assertion {

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
        return "Object of class [" . get_class($this->object) . "] should be of class [{$this->class}]";
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return is_a($this->object, $this->class);
    }
}