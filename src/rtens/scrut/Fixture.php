<?php
namespace rtens\scrut;

abstract class Fixture {

    /** @var Asserter */
    protected $assert;

    /**
     * @param Asserter $assert <-
     */
    function __construct(Asserter $assert) {
        $this->assert = $assert;
        $this->init();
    }

    protected function init() {
    }

} 