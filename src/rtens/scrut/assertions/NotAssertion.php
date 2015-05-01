<?php
namespace rtens\scrut\assertions;

use rtens\scrut\Assertion;

class NotAssertion implements Assertion {

    /** @var Assertion */
    private $assertion;

    function __construct(Assertion $assertion) {
        $this->assertion = $assertion;
    }

    /**
     * @return string
     */
    public function describeFailure() {
        return str_replace('should', 'should not', $this->assertion->describeFailure());
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return !$this->assertion->checksOut();
    }
}