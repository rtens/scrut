<?php
namespace watoki\scrut\tests\plain;

use watoki\factory\Factory;
use watoki\scrut\Test;
use watoki\scrut\TestName;
use watoki\scrut\tests\TestCase;
use watoki\scrut\tests\TestSuite;

class PlainTestSuite extends TestSuite {

    /** @var object */
    private $suite;

    /** @var callable */
    private $methodFilter;

    /** @var Factory */
    protected $factory;

    /**
     * @param Factory $factory <-
     * @param object $suite
     * @param null|TestName $parent
     */
    function __construct(Factory $factory, $suite, TestName $parent = null) {
        parent::__construct($parent);
        $this->factory = $factory;
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
     * @return TestName
     */
    public function getName() {
        return new TestName(get_class($this->suite));
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

        foreach ($filtered as $method) {
            yield $this->createTestCase($method);
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @return TestCase
     */
    protected function createTestCase(\ReflectionMethod $method) {
        return new PlainTestCase($this->factory, $method, $this->getName());
    }

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new PlainFailureSourceLocator(new \ReflectionClass($this->suite));
    }
}