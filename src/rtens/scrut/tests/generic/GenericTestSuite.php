<?php
namespace rtens\scrut\tests\generic;

use rtens\scrut\Test;
use rtens\scrut\TestName;
use rtens\scrut\tests\TestSuite;

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
     * @param \Exception $creation Used for determining source location
     */
    function __construct($name, TestName $parent = null, \Exception $creation = null) {
        parent::__construct($parent);
        $this->name = $name;
        $this->creation = $creation ?: new \Exception();
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
        return $this->add(new GenericTestCase($name, $callback, $this->getName(), new \Exception()));
    }

    /**
     * Adds a new GenericTestSuite
     * @param string $name
     * @param callable $configureSuite Receives the new GenericTestSuite
     * @return GenericTestSuite
     */
    public function suite($name, callable $configureSuite = null) {
        $suite = new GenericTestSuite($name, $this->getName(), new \Exception());

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
     * @return \rtens\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new GenericFailureSourceLocator($this->creation);
    }
}