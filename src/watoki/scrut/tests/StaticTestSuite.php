<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;
use watoki\scrut\Failure;
use watoki\scrut\failures\IncompleteTestFailure;

abstract class StaticTestSuite extends PlainTestSuite {

    /** @var Asserter */
    protected $assert;

    function __construct() {
        parent::__construct($this);
    }

    protected function before() {
    }

    protected function after() {
    }

    protected function createTestCase(\ReflectionMethod $method) {
        return new StaticTestCase($method);
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

    protected function fail($message = "") {
        throw new Failure($message);
    }

    protected function pass() {
        $this->assert(true);
    }

    protected function assert($condition, $equals = true) {
        $this->assert->__invoke($condition, $equals);
    }

    protected function markIncomplete($message = "") {
        throw new IncompleteTestFailure($message);
    }
}