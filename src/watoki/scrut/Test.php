<?php
namespace watoki\scrut;

abstract class Test {

    /**
     * @var null|TestName
     */
    private $parent;

    public function __construct(TestName $parent = null) {
        $this->parent = $parent ?: new TestName([]);
    }

    /**
     * @return TestName
     */
    public function getName() {
        return $this->parent;
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
}