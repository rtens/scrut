<?php
namespace watoki\scrut\tests\migration;

use watoki\factory\Factory;
use watoki\scrut\TestName;
use watoki\scrut\tests\statics\StaticTestSuite;

class PhpUnitTestSuite extends StaticTestSuite {

    /**
     * @param Factory $factory <-
     * @param TestName $parent
     * @throws \Exception
     */
    function __construct(Factory $factory, TestName $parent = null) {
        parent::__construct($factory, $parent);

        $this->setMethodFilter(function (\ReflectionMethod $method) {
            return substr(strtolower($method->getName()), 0, 4) == 'test'
                || strpos(strtolower($method->getDocComment()), '@test');
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