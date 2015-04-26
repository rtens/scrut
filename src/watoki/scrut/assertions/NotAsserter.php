<?php
namespace watoki\scrut\assertions;

use watoki\scrut\Asserter;
use watoki\scrut\Assertion;

class NotAsserter extends Asserter {

    /** @var Asserter */
    private $parent;

    function __construct(Asserter $parent) {
        $this->parent = $parent;
    }

    public function assert(Assertion $assertion, $message = "") {
        $this->parent->assert(new NotAssertion($assertion), $message);
    }
}