<?php
namespace watoki\scrut;

use watoki\factory\Factory;

abstract class Test {

    /** @var null|TestName */
    private $parent;

    /** @var Factory */
    protected $factory;

    public function __construct(TestName $parent = null, Factory $factory = null) {
        $this->parent = $parent ?: new TestName([]);
        $this->factory = $factory ?: new Factory();
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