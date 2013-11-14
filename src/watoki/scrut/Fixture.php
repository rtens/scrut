<?php
namespace watoki\scrut;

use watoki\factory\Factory;

abstract class Fixture {

    public static $CLASS = __CLASS__;

    protected $spec;

    public function __construct(Specification $spec, Factory $factory) {
        $this->spec = $spec;

        $this->setUp();
    }

    protected function setUp() {}

}
