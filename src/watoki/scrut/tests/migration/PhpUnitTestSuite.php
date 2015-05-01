<?php
namespace watoki\scrut\tests\migration;

use watoki\scrut\TestName;
use watoki\scrut\tests\statics\StaticTestSuite;
use watoki\scrut\tests\TestFilter;

class PhpUnitTestSuite extends StaticTestSuite {

    /**
     * @param TestFilter $filter
     * @param TestName $parent
     * @throws \Exception
     */
    function __construct(TestFilter $filter, TestName $parent = null) {
        parent::__construct($filter
            ->filterMethod(function (\ReflectionMethod $method) {
                return $this->isTestMethod($method);
            }), $parent);

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

    public function fail($message = "") {
        parent::fail($message);
    }

    protected function isTestMethod(\ReflectionMethod $method) {
        return strtolower(substr($method->getName(), 0, 4)) == 'test'
        || strpos(strtolower($method->getDocComment()), '@test');
    }
}