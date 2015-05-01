<?php
namespace watoki\scrut\tests\statics;

use watoki\factory\Factory;
use watoki\factory\Injector;
use watoki\factory\providers\DefaultProvider;
use watoki\scrut\Asserter;
use watoki\scrut\TestName;
use watoki\scrut\tests\plain\PlainTestSuite;
use watoki\scrut\tests\TestFilter;

abstract class StaticTestSuite extends PlainTestSuite {

    /** @var Asserter */
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

    public function execute($method, Asserter $assert) {
        $this->assert = $assert;

        $this->injectProperties();

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

    protected function injectProperties() {
        $provider = new DefaultProvider(new Factory());
        $injector = new Injector(new Factory());
        $injector->injectPropertyAnnotations($this, $provider->getAnnotationFilter());
        $injector->injectProperties($this, $provider->getPropertyFilter());
    }
}