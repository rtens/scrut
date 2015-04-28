<?php
namespace watoki\scrut;

abstract class Test {

    /**
     * @var null|TestName
     */
    private $parent;

    public function __construct(TestName $parent = null) {
        $this->parent = $parent ?: new TestName();
    }

    /**
     * @param TestRunListener $listener
     * @return void
     */
    abstract public function run(TestRunListener $listener);

    /**
     * @return TestName
     */
    final public function getName() {
        return $this->parent->with($this->getOwnName());
    }

    /**
     * @return string
     */
    abstract protected function getOwnName();

    /**
     * @return \watoki\scrut\tests\FailureSourceLocator
     */
    abstract protected function getFailureSourceLocator();
}