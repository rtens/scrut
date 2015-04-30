<?php
namespace watoki\scrut\tests\generic;

use watoki\factory\Factory;
use watoki\scrut\Test;
use watoki\scrut\TestName;
use watoki\scrut\tests\TestSuite;

class GenericTestSuite extends TestSuite {

    /** @var \Exception */
    private $creation;

    /** @var Test[] */
    private $tests = [];

    /** @var string */
    private $name;

    /**
     * @param string $name
     * @param null|TestName $parent
     * @param Factory $factory
     */
    function __construct($name, TestName $parent = null, Factory $factory = null) {
        parent::__construct($parent, $factory);
        $this->name = $name;
        $this->creation = new \Exception();
    }

    /**
     * @return TestName
     */
    public function getName() {
        return parent::getName()->with($this->name);
    }

    /**
     * @param Test $test
     * @return $this
     */
    public function add(Test $test) {
        $this->tests[] = $test;
        return $this;
    }

    /**
     * Adds a new GenericTestCase with $name and $callback
     * @param string $name
     * @param callable $callback
     * @return $this
     */
    public function test($name, callable $callback) {
        return $this->add(new GenericTestCase($callback, $name, $this->getName(), $this->factory));
    }

    /**
     * Adds a new GenericTestSuite
     * @param string $name
     * @param callable $configureSuite Receives the new GenericTestSuite
     * @return GenericTestSuite
     */
    public function suite($name, callable $configureSuite = null) {
        $suite = new GenericTestSuite($name, $this->getName(), $this->factory);
        if ($configureSuite) {
            $configureSuite($suite);
        }
        return $this->add($suite);
    }

    /**
     * @return Test[]
     */
    protected function getTests() {
        return $this->tests;
    }

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new GenericFailureSourceLocator($this->creation);
    }
}