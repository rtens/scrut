<?php
namespace spec\watoki\scrut;

use watoki\scrut\fixtures\ExceptionFixture;
use watoki\scrut\tests\statics\StaticTestSuite;

class AssertExceptions extends StaticTestSuite {

    /** @var ExceptionFixture */
    private $try;

    function before() {
        $this->try = new ExceptionFixture($this->assert);
    }

    function catchExceptions() {
        $this->try->tryTo(function () {
            throw new \Exception;
        });
        $this->try->thenAnExceptionShouldBeThrown();
    }

    function noExceptionsToCatch() {
        $this->try->tryTo(function () {
        });
        $this->try->thenNoExceptionShouldBeThrown();
    }

    function assertMessage() {
        $this->try->tryTo(function () {
            throw new \Exception('foo');
        });
        $this->try->thenTheException_ShouldBeThrown('foo');
    }

    function assertMessageParts() {
        $this->try->tryTo(function () {
            throw new \Exception('foobar');
        });
        $this->try->thenAnExceptionContaining_ShouldBeThrown('foo');
        $this->try->thenAnExceptionContaining_ShouldBeThrown('bar');
    }

    function assertExceptionType() {
        $this->try->tryTo(function () {
            throw new \InvalidArgumentException();
        });
        $this->try->thenAnExceptionShouldBeThrown();
        $this->try->thenA_ShouldBeThrown(\LogicException::class);
        $this->try->thenA_ShouldBeThrown(\InvalidArgumentException::class);
    }
} 