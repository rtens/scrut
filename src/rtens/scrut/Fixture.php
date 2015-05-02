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
        $this->init();
    }

    protected function init() {
    }

} 