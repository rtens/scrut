<?php
namespace watoki\scrut\assertions;

use watoki\scrut\Assertion;

class CountAssertion implements Assertion {

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
        return "Counted [" . count($this->countable) . "] but expected [{$this->count}]";
    }

    /**
     * @return bool
     */
    public function checksOut() {
        return count($this->countable) == $this->count;
    }
}