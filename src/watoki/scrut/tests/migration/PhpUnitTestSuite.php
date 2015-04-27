<?php
namespace watoki\scrut\tests\migration;

use watoki\scrut\tests\statics\StaticTestSuite;

class PhpUnitTestSuite extends StaticTestSuite {

    function __construct() {
        parent::__construct();

        $this->setMethodFilter(function (\ReflectionMethod $method) {
            return substr($method->getName(), 0, 4) == 'test';
        });

        if (!class_exists(\PHPUnit_Framework_Assert::class)) {
            throw new \Exception("You must install phpunit/phpunit in order to use this class");
        }
    }

    protected function setUp() {
    }

    protected function tearDown() {
    }

    protected function before() {
        $this->pass();
        $this->setUp();
    }

    protected function after() {
        $this->tearDown();
    }

    function __call($name, $arguments) {
        call_user_func_array(array(\PHPUnit_Framework_Assert::class, $name), $arguments);
    }
}