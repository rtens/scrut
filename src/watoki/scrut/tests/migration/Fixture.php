<?php
namespace watoki\scrut\tests\migration;

class Fixture {

    /** @var Specification|\PHPUnit_Framework_Assert */
    protected $spec;

    function __construct(Specification $spec) {
        $this->spec = $spec;
    }

    public function setUp() {
    }
}