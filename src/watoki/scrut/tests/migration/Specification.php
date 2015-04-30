<?php
namespace watoki\scrut\tests\migration;

use watoki\factory\Factory;
use watoki\factory\Injector;

class Specification extends PhpUnitTestSuite {

    /** @var array|callable[] */
    public $undos = [];

    /** @var Factory <- */
    protected $factory;

    protected function before() {
        parent::before();
        $this->factory = new Factory();
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
        $factory = new Factory();
        $factory->setProvider(Fixture::class, new FixtureProvider($this, $factory));

        $injector = new Injector($factory);
        $injector->injectPropertyAnnotations($this, function () {
            return true;
        });
    }

}