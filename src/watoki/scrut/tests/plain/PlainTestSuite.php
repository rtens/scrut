<?php
namespace watoki\scrut\tests\plain;

use watoki\scrut\Test;
use watoki\scrut\TestName;
use watoki\scrut\tests\TestCase;
use watoki\scrut\tests\TestFilter;
use watoki\scrut\tests\TestSuite;

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
    protected function getTests() {
        $methods = (new \ReflectionClass($this->suite))->getMethods();

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
            yield $this->createTestCase($method);
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @return TestCase
     */
    protected function createTestCase(\ReflectionMethod $method) {
        return new PlainTestCase($method, $this->getName());
    }

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new PlainFailureSourceLocator(new \ReflectionClass($this->suite));
    }
}