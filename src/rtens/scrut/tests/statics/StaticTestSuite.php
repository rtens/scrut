<?php
namespace rtens\scrut\tests\statics;

use rtens\scrut\Assert;
use rtens\scrut\TestName;
use rtens\scrut\tests\plain\PlainTestSuite;
use rtens\scrut\tests\TestFilter;

abstract class StaticTestSuite extends PlainTestSuite {

    /** @var Assert <- */
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

    /**
     * @param string $method
     * @param Assert $assert
     * @throws \Exception
     */
    public function execute($method, Assert $assert) {
        $this->before();

        try {
            $this->$method($assert);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->after();
        }
    }

    protected function createTestCase(\ReflectionMethod $method) {
        return new StaticTestCase($method, $this->getName());
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
}