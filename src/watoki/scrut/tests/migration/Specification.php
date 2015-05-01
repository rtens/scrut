<?php
namespace watoki\scrut\tests\migration;

use watoki\factory\Factory;
use watoki\factory\Injector;

class Specification extends PhpUnitTestSuite {

    /** @var array|callable[] */
    public $undos = [];

    /** @var Factory <- */
    public $factory;

    protected function before() {
        parent::before();
        $this->background();
    }

    protected function background() {
    }

    protected function after() {
        foreach ($this->undos as $undo) {
            $undo();
        }
        parent::after();
    }

    protected function injectProperties() {
        $this->factory = new Factory();

        $factory = new Factory();
        $provider = new FixtureProvider($this, $factory);
        $factory->setProvider(Fixture::class, $provider);

        $injector = new Injector($factory);
        $injector->injectPropertyAnnotations($this, function () {
            return true;
        });

        foreach ($provider->getProvidedFixtures() as $fixture) {
            $fixture->setUp();
        }
    }

}