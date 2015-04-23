<?php
namespace watoki\scrut\tests;

use watoki\scrut\Test;

class GenericTestSuite extends TestSuite {

    /** @var Test[] */
    private $tests = [];

    /** @var string */
    private $name;

    function __construct($name) {
        $this->name = $name;
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
}