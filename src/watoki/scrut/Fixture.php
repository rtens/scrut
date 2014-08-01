<?php
namespace watoki\scrut;

use watoki\factory\Factory;

abstract class Fixture {

    public static $CLASS = __CLASS__;

    protected $spec;

    public function __construct(Specification $spec, Factory $factory) {
        $this->spec = $spec;
    }

    /**
     * Is called by Specification::setUp
     */
    public function setUp() {}

    /**
     * Is called by Specification::tearDown
     */
    public function tearDown() {}

}
