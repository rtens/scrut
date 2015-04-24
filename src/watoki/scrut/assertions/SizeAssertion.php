<?php
namespace watoki\scrut\assertions;

class SizeAssertion extends ValueAssertion {

    /** @var array|mixed */
    private $countable;

    /** @var int */
    private $count;

    /**
     * @param array|mixed $countable
     * @param int $count
     */
    function __construct($countable, $count) {
        $this->countable = $countable;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function describeFailure() {
        if (is_string($this->countable)) {
            return $this->export($this->countable) . " should have length " . $this->export($this->count);
        } else if (is_array($this->countable) || is_object($this->countable)) {
            return "Counted size " . $this->export(count($this->countable)) . " should be " . $this->export($this->count);
        } else {
            return $this->export($this->countable) . " is not countable";
        }
    }

    /**
     * @return bool
     */
    public function checksOut() {
        if (is_string($this->countable)) {
            return strlen($this->countable) == $this->count;
        } else if (is_array($this->countable) || is_object($this->countable)) {
            return count($this->countable) == $this->count;
        } else {
            return false;
        }
    }
}