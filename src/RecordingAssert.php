<?php
namespace rtens\scrut;

class RecordingAssert extends Assert {

    /**
     * @var array|Assertion[]
     */
    private $assertions = [];

    public function that(Assertion $assertion, $message = "") {
        $this->add($assertion);
        parent::that($assertion, $message);
    }

    private function add(Assertion $assertion) {
        $this->assertions[] = $assertion;
    }

    public function hasMadeAssertions() {
        return !empty($this->assertions);
    }
}