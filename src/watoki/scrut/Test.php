<?php
namespace watoki\scrut;

use watoki\factory\Factory;

abstract class Test {

    /** @var null|TestName */
    private $parent;

    /** @var Factory */
    protected $factory;

    /**
     * @param Factory $factory <-
     * @param TestName $parent
     */
    public function __construct(Factory $factory, TestName $parent = null) {
        $this->factory = $factory;
        $this->parent = $parent ?: new TestName([]);
    }

    /**
     * @param TestRunListener $listener
     * @return void
     */
    abstract public function run(TestRunListener $listener);

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    abstract protected function getFailureSourceLocator();

    /**
     * @return TestName
     */
    public function getName() {
        return $this->parent;
    }
}