<?php
namespace watoki\scrut;

class TestName {

    private $parts;

    /**
     * @param array|string $parts
     */
    function __construct($parts) {
        $this->parts = is_array($parts) ? $parts : [$parts];
    }

    public function with($childName) {
        return new TestName(array_merge($this->parts, [$childName]));
    }

    public function last() {
        return end($this->parts);
    }

    public function __toString() {
        return $this->toString();
    }

    public function toString() {
        return implode('::', $this->parts);
    }

}