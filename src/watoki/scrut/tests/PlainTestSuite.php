<?php
namespace watoki\scrut\tests;

use watoki\scrut\Test;

class PlainTestSuite extends TestSuite {

    /** @var object */
    private $suite;

    /** @var callable */
    private $methodFilter;

    /**
     * @param object $suite
     */
    function __construct($suite) {
        $this->suite = $suite;

        $this->methodFilter = function (\ReflectionMethod $method) {
            return $method->getDeclaringClass()->getName() == get_class($this->suite)
            && substr($method->getName(), 0, 1) != '_'
            && $method->getName() != 'before'
            && $method->getName() != 'after'
            && !strpos($method->getDocComment(), '@internal')
            && !$method->isConstructor()
            && !$method->isStatic()
            && $method->isPublic();
        };
    }

    /**
     * @return string
     */
    public function getName() {
        return get_class($this->suite);
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
     * @return object
     */
    public function getSuite() {
        return $this->suite;
    }

    /**
     * @return Test[]
     */
    protected function getTests() {
        $methods = (new \ReflectionClass($this->suite))->getMethods();
        $filtered = array_filter($methods, $this->methodFilter);

        return array_map(function (\ReflectionMethod $method) {
            return $this->createTestCase($method);
        }, $filtered);
    }

    /**
     * @param \ReflectionMethod $method
     * @return TestCase
     */
    protected function createTestCase(\ReflectionMethod $method) {
        return new PlainTestCase($method);
    }
}