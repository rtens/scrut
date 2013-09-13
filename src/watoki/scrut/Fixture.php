<?php
namespace watoki\scrut;

use watoki\factory\Factory;

abstract class Fixture {

    protected $spec;

    public function __construct(Specification $spec, Factory $factory) {
        $this->spec = $spec;
        $this->makeSingleton($factory);
        $this->loadDependencies();
    }

    protected function makeSingleton(Factory $factory) {
        $factory->setSingleton(get_class($this), $this);
    }

    protected function loadDependencies() {
        $spec = $this->spec;
        $injector = new Injector($this);
        $injector->injectAnnotatedProperties(function ($class) use ($spec) {
            return $spec->useFixture($class);
        });
    }

}
