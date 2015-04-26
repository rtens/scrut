<?php
namespace watoki\scrut;

use watoki\scrut\tests\TestSuite;

class Fixture {

    /** @var Asserter */
    protected $assert;

    /** @var TestSuite */
    protected $suite;

    function __construct(TestSuite $suite, Asserter $assert) {
        $this->suite = $suite;
        $this->assert = $assert;
    }

} 