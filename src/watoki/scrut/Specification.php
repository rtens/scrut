<?php
namespace watoki\scrut;

use rtens\mockster\ClassResolver;
use watoki\factory\Factory;

abstract class Specification extends \PHPUnit_Framework_TestCase {

    /** @var Factory */
    protected $factory;

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

    public function useFixture($class) {
        return $this->factory->getInstance($class, array('spec' => $this));
    }

    protected function loadDependencies() {
        $that = $this;
        $injector = new Injector($this);
        $injector->injectAnnotatedProperties(function ($class) use ($that) {
            return $that->useFixture($class);
        });
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