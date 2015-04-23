<?php
namespace watoki\scrut;

class AsserterProxy extends Asserter {

    /**
     * @var array|Assertion[]
     */
    private $assertions = [];

    public function assert(Assertion $assertion, $message = "") {
        $this->add($assertion);
        parent::assert($assertion, $message);
    }

    private function add(Assertion $assertion) {
        $this->assertions[] = $assertion;
    }

    public function hasAssertions() {
        return !empty($this->assertions);
    }
}