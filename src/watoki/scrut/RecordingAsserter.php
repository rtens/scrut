<?php
namespace watoki\scrut;

class RecordingAsserter extends Asserter {

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

    public function hasMadeAssertions() {
        return !empty($this->assertions);
    }
}