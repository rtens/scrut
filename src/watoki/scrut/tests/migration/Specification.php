<?php
namespace watoki\scrut\tests\migration;

use watoki\factory\Factory;
use watoki\scrut\TestName;

class Specification extends PhpUnitTestSuite {

    /** @var array|callable[] */
    public $undos = [];

    /**
     * @param Factory $factory <-
     * @param TestName $parent
     * @throws \Exception
     */
    function __construct(Factory $factory, TestName $parent = null) {
        parent::__construct($factory, $parent);

        $factory->setProvider(Fixture::class, new FixtureProvider($this, $factory));
    }

    protected function before() {
        parent::before();
        $this->background();
    }

    protected function background() {
    }

    protected function after() {
        foreach ($this->undos as $undo) {
            $undo();
        }
        parent::after();
    }

}