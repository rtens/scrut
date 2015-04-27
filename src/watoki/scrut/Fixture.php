<?php
namespace watoki\scrut;

class Fixture {

    /** @var Asserter */
    protected $assert;

    function __construct(Asserter $assert) {
        $this->assert = $assert;
    }

} 