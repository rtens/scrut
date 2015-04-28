<?php
namespace watoki\scrut\tests\generic;

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
     */
    function __construct($name, TestName $parent = null) {
        parent::__construct($parent);
        $this->name = $name;
        $this->creation = new \Exception();
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
     */
    public function test($name, callable $callback) {
        $this->add(new GenericTestCase($callback, $name, $this->getName()));
    }

    /**
     * @return Test[]
     */
    protected function getTests() {
        return $this->tests;
    }

    /**
     * @return string
     */
    protected function getOwnName() {
        return $this->name;
    }

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    protected function getFailureSourceLocator() {
        return new GenericFailureSourceLocator($this->creation);
    }
}