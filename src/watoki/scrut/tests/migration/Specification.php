<?php
namespace watoki\scrut\tests\migration;

use watoki\factory\Factory;

class Specification extends PhpUnitTestSuite {

    /** @var Factory */
    protected $factory;

    function __construct() {
        parent::__construct();

        $this->factory = new Factory();
    }

    protected function before() {
        parent::before();
        $this->background();
    }

    protected function background() {
    }

}