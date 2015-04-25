<?php
namespace watoki\scrut\tests;

class PhpUnitCompatibleTestSuite extends StaticTestSuite {

    function __construct() {
        parent::__construct();

        $this->setMethodFilter(function (\ReflectionMethod $method) {
            return substr($method->getName(), 0, 4) == 'test';
        });

        if (!class_exists(\PHPUnit_Framework_Assert::class)) {
            throw new \Exception("You must install PHPUnit in order to use this class");
        }
    }

    protected function setUp() {
    }

    protected function tearDown() {
    }

    protected function before() {
        $this->setUp();
    }

    protected function after() {
        $this->tearDown();
    }

    function __call($name, $arguments) {
        call_user_func_array(array(\PHPUnit_Framework_Assert::class, $name), $arguments);
    }
}