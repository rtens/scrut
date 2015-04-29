<?php
namespace watoki\scrut;

use watoki\factory\Factory;
use watoki\factory\providers\DefaultProvider;

abstract class Test {

    /**
     * @var null|TestName
     */
    private $parent;

    /** @var Factory */
    private $factory;

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

    /**
     * @param Factory $factory
     * @return $this
     */
    protected function setFactory($factory) {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @return \watoki\factory\Factory
     */
    protected function getFactory() {
        if (!$this->factory) {
            $this->factory = new Factory();
            $provider = new DefaultProvider($this->factory);
            $provider->setAnnotationFilter(function () {
                return true;
            });
            $this->factory->setProvider(\StdClass::class, $provider);
        }
        return $this->factory;
    }
}