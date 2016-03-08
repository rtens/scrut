<?php
namespace rtens\scrut;

abstract class Fixture {

    /** @var Assert */
    protected $assert;

    /**
     * @param Assert $assert <-
     */
    function __construct(Assert $assert) {
        $this->assert = $assert;
    }

    public function before() {
    }

    public function after() {
    }
} 