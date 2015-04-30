<?php
namespace watoki\scrut\tests\migration;

class Fixture {

    /** @var Specification */
    protected $spec;

    function __construct(Specification $spec) {
        $this->spec = $spec;
    }
}