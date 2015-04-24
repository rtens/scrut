<?php
namespace watoki\scrut\tests;

use watoki\scrut\Test;

class GenericTestSuite extends TestSuite {

    /** @var Test[] */
    private $tests = [];

    /** @var string */
    private $name;

    /** @var \Exception */
    private $creation;

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
     * @return \Exception
     */
    public function getCreation() {
        return $this->creation;
    }
}