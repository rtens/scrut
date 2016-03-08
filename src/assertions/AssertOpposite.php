<?php
namespace rtens\scrut\assertions;

use rtens\scrut\Assert;
use rtens\scrut\Assertion;

class AssertOpposite extends Assert {

    /** @var Assert */
    private $parent;

    function __construct(Assert $parent) {
        $this->parent = $parent;
    }

    public function that(Assertion $assertion, $message = "") {
        $this->parent->that(new OppositeAssertion($assertion), $message);
    }
}