<?php
namespace watoki\scrut\tests\statics;

use watoki\scrut\Asserter;
use watoki\scrut\TestName;
use watoki\scrut\tests\plain\PlainTestSuite;

abstract class StaticTestSuite extends PlainTestSuite {

    /** @var Asserter */
    protected $assert;

    function __construct(TestName $parent = null) {
        parent::__construct($this, $parent);
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
        $this->before();

        try {
            $this->$method();
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
}