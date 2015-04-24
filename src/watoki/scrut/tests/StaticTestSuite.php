<?php
namespace watoki\scrut\tests;

use watoki\scrut\Asserter;
use watoki\scrut\Failure;
use watoki\scrut\failures\IncompleteTestFailure;

abstract class StaticTestSuite extends TestSuite {

    /** @var callable */
    private $methodFilter;

    /** @var Asserter */
    protected $assert;

    function __construct() {
        $this->methodFilter = function (\ReflectionMethod $method) {
            return $method->getDeclaringClass()->getName() == get_class($this)
                && substr($method->getName(), 0, 1) != '_'
                && !strpos($method->getDocComment(), '@internal')
                && !$method->isConstructor()
                && !$method->isStatic()
                && $method->isPublic();
        };
    }

    protected function before() {
    }

    protected function after() {
    }

    public function getName() {
        return get_class($this);
    }

    /**
     * @param callable $filter
     */
    public function setMethodFilter(callable $filter) {
        $this->methodFilter = $filter;
    }

    /**
     * @return callable
     */
    public function getMethodFilter() {
        return $this->methodFilter;
    }

    /**
     * @return \watoki\scrut\Test[]
     */
    protected function getTests() {
        $filtered = array_filter((new \ReflectionClass($this))->getMethods(), $this->methodFilter);

        return array_map(function (\ReflectionMethod $method) {
            return new StaticTestCase($method);
        }, $filtered);
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

    protected function assert($condition, $equals = true) {
        $this->assert->__invoke($condition, $equals);
    }

    protected function markIncomplete($message = "") {
        throw new IncompleteTestFailure($message);
    }

}