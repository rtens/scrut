<?php
namespace watoki\scrut;

use rtens\mockster\ClassResolver;
use watoki\factory\Factory;
use watoki\factory\Injector;

abstract class Specification extends \PHPUnit_Framework_TestCase {

    /** @var Factory */
    public $factory;

    public $undos = array();

    protected function background() {}

    protected function setUp() {
        $this->factory = new Factory();
        $this->loadDependencies();
    }

    protected function runTest() {
        $this->background();
        return parent::runTest();
    }

    protected function tearDown() {
        foreach ($this->undos as $undo) {
            $undo();
        }
    }

    protected function loadDependencies() {
        $provider = new FixtureProvider($this);
        $this->factory->setProvider(Fixture::$CLASS, $provider);
        $injector = new Injector($this->factory);
        $injector->injectPropertyAnnotations($this, $provider->getAnnotationFilter());
    }

    public function runAllScenarios($prefix = 'test') {
        $me = get_class($this);
        $result = new \PHPUnit_Framework_TestResult();

        foreach (get_class_methods($this) as $method) {
            if (substr($method, 0, strlen($prefix)) == $prefix) {
                /** @var Specification $spec */
                $spec = new $me($method);
                $spec->run($result);
            }
        }

        return $result;
    }

}