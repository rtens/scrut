<?php
namespace watoki\scrut;

abstract class Test {

    /** @var null|TestName */
    private $parent;

    /**
     * @param TestName $parent
     */
    public function __construct(TestName $parent = null) {
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