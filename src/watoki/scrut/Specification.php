<?php
namespace watoki\scrut;

use watoki\factory\Factory;
use watoki\factory\Injector;

abstract class Specification extends \PHPUnit_Framework_TestCase {

    /** @var \watoki\scrut\FixtureProvider */
    private $provider;

    /** @var Factory */
    public $factory;

    /**
     * @var array|callable[] invoked by tearDown
     */
    public $undos = array();

    protected function background() {}

    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $factory = new Factory();

        $this->provider = new FixtureProvider($this, $factory);
        $factory->setProvider(Fixture::$CLASS, $this->provider);

        $injector = new Injector($factory);
        $injector->injectPropertyAnnotations($this, array($this, 'annotationPropertyFilter'));
    }

    public function annotationPropertyFilter($annotation) {
        return strpos($annotation, '<-') !== false;
    }

    protected function setUp() {
        $this->factory = new Factory();

        $this->onFixtures(function (Fixture $fixture) {
            $fixture->setUp();
        });
    }

    protected function runTest() {
        $this->background();
        return parent::runTest();
    }

    protected function tearDown() {
        $this->onFixtures(function (Fixture $fixture) {
            $fixture->tearDown();
        });

        foreach ($this->undos as $undo) {
            $undo();
        }
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

    /**
     * @param callable $do
     */
    private function onFixtures($do) {
        foreach ($this->provider->getProvidedFixtures() as $fixture) {
            $do($fixture);
        }
    }

}