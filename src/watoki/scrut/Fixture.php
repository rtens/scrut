<?php
namespace watoki\scrut;

use watoki\factory\Factory;

abstract class Fixture {

    protected $test;

    public function __construct(TestCase $test, Factory $factory) {
        $this->test = $test;
        $this->makeSingleton($factory);
        $this->loadDependencies();
    }

    protected function makeSingleton(Factory $factory) {
        $factory->setSingleton(get_class($this), $this);
    }

    protected function loadDependencies() {
        $test = $this->test;
        $injector = new Injector($this);
        $injector->injectProperties(function ($class) use ($test) {
            return $test->useFixture($class);
        });
    }

}
