<?php
namespace watoki\scrut\tests\generic;

use watoki\scrut\Test;
use watoki\scrut\tests\TestSuite;

class GenericTestSuite extends TestSuite {

    /** @var \Exception */
    private $creation;

    /** @var Test[] */
    private $tests = [];

    /** @var string */
    private $name;

    function __construct($name) {
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
        $this->add(new GenericTestCase($name, $callback));
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
    public function getName() {
        return $this->name;
    }

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    public function getFailureSourceLocator() {
        return new GenericFailureSourceLocator($this->creation);
    }
}