<?php
namespace rtens\scrut\tests\plain;

use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\tests\TestCase;
use rtens\scrut\tests\TestFilter;
use rtens\scrut\tests\TestSuite;

class PlainTestSuite extends TestSuite {

    /** @var object */
    private $suite;

    /** @var TestFilter */
    private $filter;

    /**
     * @param TestFilter $filter
     * @param string $suite
     * @param null|TestName $parent
     */
    function __construct(TestFilter $filter, $suite, TestName $parent = null) {
        parent::__construct($parent);
        $this->filter = $filter;
        $this->suite = $suite;
    }

    /**
     * @return TestName
     */
    public function getName() {
        return new TestName($this->suite);
    }

    /**
     * @return Test[]
     */
    public function getTests() {
        $class = new \ReflectionClass($this->suite);
        $methods = $class->getMethods();

        $filtered = array_filter($methods, function (\ReflectionMethod $method) {
            return $this->filter->acceptsMethod($method)
            && $method->getDeclaringClass()->getName() == $this->suite
            && substr($method->getName(), 0, 1) != '_'
            && $method->getName() != PlainTestCase::BEFORE_METHOD
            && $method->getName() != PlainTestCase::AFTER_METHOD
            && !$method->isConstructor()
            && !$method->isStatic()
            && $method->isPublic();
        });

        foreach ($filtered as $method) {
            yield $this->createTestCase($class, $method);
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @return TestCase
     */
    protected function createTestCase(\ReflectionClass $class, \ReflectionMethod $method) {
        return new PlainTestCase($class, $method, $this->getName());
    }

    /**
     * @return \rtens\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new PlainFailureSourceLocator(new \ReflectionClass($this->suite));
    }
}