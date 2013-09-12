<?php
namespace watoki\scrut;
 
use watoki\factory\Factory;

abstract class Fixture {

    protected $test;

    protected $factory;

    public function __construct(TestCase $test, Factory $factory) {
        $factory->setSingleton(get_class($this), $this);

        $this->test = $test;
        $this->factory = $factory;
    }

}
