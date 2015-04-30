<?php
namespace watoki\scrut;

class Fixture {

    /** @var Asserter */
    protected $assert;

    /**
     * @param Asserter $assert <-
     */
    function __construct(Asserter $assert) {
        $this->assert = $assert;
    }

} 