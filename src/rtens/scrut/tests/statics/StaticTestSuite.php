<?php
namespace rtens\scrut\tests\statics;

use watoki\factory\Factory;
use watoki\factory\Injector;
use watoki\factory\providers\DefaultProvider;
use rtens\scrut\Assert;
use rtens\scrut\TestName;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\TestFilter;

abstract class StaticTestSuite extends PlainTestSuite {

    /** @var Assert */
    protected $assert;

    /**
     * @param TestFilter $filter
     * @param TestName $parent
     */
    function __construct(TestFilter $filter, TestName $parent = null) {
        parent::__construct($filter, get_class($this), $parent);
    }

    protected function before() {
    }

    protected function after() {
    }

    protected function createTestCase(\ReflectionMethod $method) {
        return new StaticTestCase($method, $this->getName());
    }

    public function execute($method, Assert $assert) {
        $this->assert = $assert;

        $this->injectProperties($assert);

        $this->before();

        try {
            $this->$method($assert);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->after();
        }
    }

    /**
     * @param mixed $condition
     * @param bool|mixed $equals
     */
    protected function assert($condition, $equals = true) {
        $this->assert->__invoke($condition, $equals);
    }

    protected function pass() {
        $this->assert->pass();
    }

    protected function fail($message = "") {
        $this->assert->fail($message);
    }

    protected function markIncomplete($message = "") {
        $this->assert->incomplete($message);
    }

    protected function injectProperties(Assert $assert) {
        $factory = new Factory();
        $factory->setSingleton(Assert::class, $assert);

        $provider = new DefaultProvider($factory);
        $injector = new Injector($factory);

        $injector->injectPropertyAnnotations($this, $provider->getAnnotationFilter());
        $injector->injectProperties($this, $provider->getPropertyFilter());
    }
}