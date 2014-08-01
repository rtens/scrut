<?php
namespace watoki\scrut;

use watoki\factory\Factory;

abstract class Fixture {

    public static $CLASS = __CLASS__;

    protected $spec;

    /**
     * @var array|callable[] invoked by tearDown
     */
    public $undos = array();

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
    public function tearDown() {
        foreach ($this->undos as $undo) {
            $undo();
        }
    }

}
